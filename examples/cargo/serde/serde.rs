use std::ffi::{CStr, CString};
use std::mem;
use std::borrow::Cow;
use std::os::raw::{c_char, c_void};
use serde::{Serialize, Deserialize};


#[derive(Serialize, Deserialize, Debug)]
struct Subject<'a> {
    subject: Cow<'a, str>,
}

#[no_mangle]
pub extern fn allocate(size: usize) -> *mut c_void {
    let mut buffer = Vec::with_capacity(size);
    let pointer = buffer.as_mut_ptr();
    mem::forget(buffer);

    pointer as *mut c_void
}

#[no_mangle]
pub extern fn deallocate(pointer: *mut c_void, capacity: usize) {
    unsafe {
        let _ = Vec::from_raw_parts(pointer, 0, capacity);
    }
}

#[no_mangle]
pub extern fn to_subject(subject: *mut c_char) -> *mut c_char {
    let subject = unsafe { CStr::from_ptr(subject).to_string_lossy() };
    let point = Subject { subject };
    let serialized = serde_json::to_string(&point).unwrap();
    unsafe { CString::from_vec_unchecked(serialized.as_bytes().to_vec()) }.into_raw()
}
