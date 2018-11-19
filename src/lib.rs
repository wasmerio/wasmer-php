extern crate wasmi;

use wasmi::{
    Error,
    ExternVal,
    ImportsBuilder,
    Module,
    ModuleInstance,
    ModuleRef,
    NopExternals,
    RuntimeValue,
    Signature,
};
use std::fs::File;
use std::io::{self, Read};
use std::path::PathBuf;

pub fn read_wasm_binary(path: &PathBuf) -> io::Result<Vec<u8>> {
    let mut file = File::open(path)?;
    let mut buffer = Vec::new();

    file.read_to_end(&mut buffer).unwrap();

    Ok(buffer)
}

pub struct WASMInstance {
    pub file_path: String,
    instance: ModuleRef,
}

impl WASMInstance {
    pub fn new(path: &PathBuf, wasm_binary: Vec<u8>) -> Result<Self, Error> {
        let module = Module::from_buffer(&wasm_binary)?;

        Ok(
            WASMInstance {
                file_path: path.to_string_lossy().into_owned(),
                instance: ModuleInstance::new(&module, &ImportsBuilder::default())?.assert_no_start(),
            }
        )
    }

    pub fn get_function_signature(&self, function_name: &str) -> Option<Signature> {
        match self.instance.export_by_name(function_name) {
            Some(ExternVal::Func(function)) => Some(function.signature().clone()),
            _ => None
        }
    }

    pub fn invoke(&self, function_name: &str, arguments: &[RuntimeValue]) -> Option<RuntimeValue> {
        self.instance.invoke_export(function_name, arguments, &mut NopExternals).expect("foo")
    }
}

pub mod ffi {
    use super::WASMInstance;
    use wasmi::{
        RuntimeValue,
        nan_preserving_float::{F32, F64},
    };
    use std::{
        ffi::CStr,
        mem,
        os::raw::c_char,
        path::PathBuf,
        ptr,
        str,
    };

    macro_rules! check_and_deref {
        ($variable:ident) => {
            {
                assert!(!$variable.is_null());

                unsafe { &*$variable }
            }
        };
    }

    macro_rules! check_and_deref_mut {
        ($variable:ident) => {
            {
                assert!(!$variable.is_null());

                unsafe { &mut *$variable }
            }
        };
    }

    macro_rules! check_and_deref_to_pathbuf {
        ($variable:ident) => {
            {
                assert!(!$variable.is_null());

                PathBuf::from(
                    unsafe {
                        String::from_utf8_unchecked(
                            CStr::from_ptr($variable).to_bytes().to_vec()
                        )
                    }
                )
            }
        }
    }

    macro_rules! check_and_deref_to_str {
        ($variable:ident) => {
            {
                assert!(!$variable.is_null());

                unsafe { str::from_utf8_unchecked(CStr::from_ptr($variable).to_bytes()) }
            }
        };
    }

    #[no_mangle]
    pub extern "C" fn wasm_read_binary(file_path: *const c_char) -> *const Vec<u8> {
        Box::into_raw(
            Box::new(
                super::read_wasm_binary(&check_and_deref_to_pathbuf!(file_path)).unwrap_or_else(|_| vec![])
            )
        )
    }

    #[no_mangle]
    pub extern "C" fn wasm_new_instance(file_path: *const c_char, wasm_binary: *const Vec<u8>) -> *mut WASMInstance {
        let file_path = check_and_deref_to_pathbuf!(file_path);
        let wasm_binary = check_and_deref!(wasm_binary).to_vec();

        match super::WASMInstance::new(&file_path, wasm_binary) {
            Ok(wasm_instance) => Box::into_raw(Box::new(wasm_instance)),
            Err(_) => ptr::null_mut(),
        }
    }

    #[repr(C)]
    pub struct Signature {
        inputs_buffer: *const ValueType,
        inputs_length: usize,
        output: *const ValueType,
    }

    #[repr(C)]
    pub enum ValueType {
        TypeI32,
        TypeI64,
        TypeF32,
        TypeF64,
        TypeVoid,
    }

    impl From<wasmi::ValueType> for ValueType {
        fn from(value_type: wasmi::ValueType) -> Self {
            use wasmi::ValueType as V;

            match value_type {
                V::I32 => ValueType::TypeI32,
                V::I64 => ValueType::TypeI64,
                V::F32 => ValueType::TypeF32,
                V::F64 => ValueType::TypeF64,
            }
        }
    }

    #[no_mangle]
    pub extern "C" fn wasm_get_function_signature(
        wasm_instance: *const WASMInstance,
        function_name: *const c_char
    ) -> *const Signature {
        let wasm_instance = check_and_deref!(wasm_instance);
        let function_name = check_and_deref_to_str!(function_name);

        match wasm_instance.get_function_signature(function_name) {
            Some(signature) => {
                let inputs: Vec<ValueType> = signature.params().iter()
                    .map(|&input| input.into())
                    .collect();

                let signature = Box::into_raw(
                    Box::new(
                        Signature {
                            inputs_buffer: inputs.as_slice().as_ptr(),
                            inputs_length: inputs.len(),
                            output: Box::into_raw(
                                Box::new(
                                    match signature.return_type() {
                                        Some(output) => output.into(),
                                        None => ValueType::TypeVoid,
                                    }
                                )
                            ),
                        }
                    )
                );

                mem::forget(inputs);

                signature
            },
            None => ptr::null()
        }
    }

    #[repr(C)]
    pub enum Value {
        I32(i32),
        I64(i64),
        F32(f32),
        F64(f64),
        None,
    }

    #[no_mangle]
    pub extern "C" fn wasm_invoke_function(
        wasm_instance: *const WASMInstance,
        function_name: *const c_char,
        arguments: *const Vec<RuntimeValue>
    ) -> *const Value {
        let wasm_instance = check_and_deref!(wasm_instance);
        let function_name = check_and_deref_to_str!(function_name);
        let arguments = check_and_deref!(arguments).as_slice();

        let value = match wasm_instance.invoke(function_name, arguments) {
            Some(runtime_value) => match runtime_value {
                RuntimeValue::I32(value) => Value::I32(value),
                RuntimeValue::I64(value) => Value::I64(value),
                RuntimeValue::F32(value) => Value::F32(value.to_float()),
                RuntimeValue::F64(value) => Value::F64(value.to_float()),
            },
            None => Value::None,
        };

        Box::into_raw(Box::new(value))
    }

    #[no_mangle]
    pub extern "C" fn wasm_invoke_arguments_builder() -> *mut Vec<RuntimeValue> {
        Box::into_raw(Box::new(Vec::new()))
    }

    #[no_mangle]
    pub extern "C" fn wasm_invoke_arguments_builder_add_i32(arguments: *mut Vec<RuntimeValue>, argument: i32) {
        check_and_deref_mut!(arguments).push(RuntimeValue::I32(argument));
    }

    #[no_mangle]
    pub extern "C" fn wasm_invoke_arguments_builder_add_i64(arguments: *mut Vec<RuntimeValue>, argument: i64) {
        check_and_deref_mut!(arguments).push(RuntimeValue::I64(argument));
    }

    #[no_mangle]
    pub extern "C" fn wasm_invoke_arguments_builder_add_f32(arguments: *mut Vec<RuntimeValue>, argument: f32) {
        check_and_deref_mut!(arguments).push(RuntimeValue::F32(F32::from_float(argument)));
    }

    #[no_mangle]
    pub extern "C" fn wasm_invoke_arguments_builder_add_f64(arguments: *mut Vec<RuntimeValue>, argument: f64) {
        check_and_deref_mut!(arguments).push(RuntimeValue::F64(F64::from_float(argument)));
    }
}


#[cfg(test)]
mod tests {
    use super::*;

    #[test]
    fn case_invoke() {
        let file_path = PathBuf::from("./tests/toy.wasm");
        let wasm_binary = read_wasm_binary(&file_path).unwrap();
        let wasm_instance = WASMInstance::new(&file_path, wasm_binary).unwrap();
        let result = wasm_instance.invoke("sum", &[1.into(), 2.into()]);

        assert_eq!(Some(3.into()), result);
    }
}
