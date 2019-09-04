extern "C" {
    fn _sum(x: i32, y: i32) -> i32;
    fn _arity_0() -> i32;
    fn _i32_i32(x: i32) -> i32;
    fn _void();
}

#[no_mangle]
pub extern "C" fn sum(x: i32, y: i32) -> i32 {
    unsafe { _sum(x, y) }
}

#[no_mangle]
pub extern "C" fn arity_0() -> i32 {
    unsafe { _arity_0() }
}

#[no_mangle]
pub extern "C" fn i32_i32(x: i32) -> i32 {
    unsafe { _i32_i32(x) }
}

#[no_mangle]
pub extern "C" fn void() {
    unsafe { _void() }
    return;
}
