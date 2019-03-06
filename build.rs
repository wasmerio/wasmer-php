extern crate walkdir;

use core::cmp::Ordering;
use std::{env::var, fs::copy, path::PathBuf};
use walkdir::{DirEntry, WalkDir};

fn is_hidden(entry: &DirEntry) -> bool {
    entry
        .file_name()
        .to_str()
        .map(|string| string.starts_with("."))
        .unwrap_or(false)
}

fn last_modified_first(a: &DirEntry, b: &DirEntry) -> Ordering {
    if let Ok(a_system_time) = a.metadata().unwrap().modified() {
        if let Ok(b_system_time) = b.metadata().unwrap().modified() {
            a_system_time.cmp(&b_system_time)
        } else {
            Ordering::Greater
        }
    } else {
        Ordering::Less
    }
}

fn main() {
    let out_directory = var("OUT_DIR").expect("The `OUT_DIR` environment variable is not found.");
    let mut build_directory = PathBuf::from(&out_directory);
    assert!(build_directory.pop(), "`OUT_DIR` is invalid.");
    assert!(build_directory.pop(), "`OUT_DIR` is invalid.");

    let mut walker = WalkDir::new(&build_directory)
        .sort_by(last_modified_first)
        .into_iter()
        .filter_entry(|entry| !is_hidden(entry))
        .filter_map(|entry| entry.ok())
        .filter(|entry| entry.metadata().is_ok())
        .filter(|entry| entry.file_name().to_string_lossy() == "wasmer.h");

    if let Some(entry) = walker.next() {
        let cargo_directory = var("CARGO_MANIFEST_DIR")
            .expect("The `CARGO_MANIFEST_DIR` environment variable is not found.");
        let mut header_destination = PathBuf::from(&cargo_directory);
        header_destination.push("extension");
        header_destination.push("wasmer.h");
        header_destination.set_extension("h");

        copy(entry.path(), header_destination)
            .expect("Cannot copy the `wasmer.h` C header file from `wasmer-runtime-c-api`.");
    } else {
        panic!("The `wasmer.h` file is not found.");
    }
}
