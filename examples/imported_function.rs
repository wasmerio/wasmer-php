extern {
    fn add(x: i32, y: i32) -> i32;
}


#[no_mangle]
pub extern fn sum(x: i32, y: i32) -> i32 {
    unsafe {
        add(x, y) + 1
    }
}
