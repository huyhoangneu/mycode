package main 
import "crypto/md5"
import "log"


import (
//      "bytes"
        "fmt"
        "os"
//      "io"
//      "io/ioutil"
        . "os"
//      "strings"
//      "syscall"
       )
var dot = []string{
    "dir_darwin.go",
    "dir_linux.go",
    "env.go",
    "error.go",
    "file.go",
    "os_test.go",
    "time.go",
    "types.go",
    "stat_darwin.go",
    "stat_linux.go",
}

var etc = []string{
    "group",
}
func testReaddirnames(dir string) {
    file, err := Open(dir, O_RDONLY, 0);
    defer file.Close();
    if err != nil {
        log.Println("open %q failed: %v", dir, err)
    }
    s, err2 := file.Readdirnames(-1);
    if err2 != nil {
        log.Println("readdirnames %q failed: %v", err2)
    }
    for _, n := range s {
        //log.Println("find file name:" , n)
        path := dir + "/" + n 
        if path != "." && path != ".." && dirExists(path) {
            testReaddirnames(path)
            //log.Println("file is dir:", path)
        } else {
            //log.Println("file name:", path)
        }
    }
}
func main() {
    testReaddirnames("/usr")
    //test map
    /*
    var timeZone = map[string] int {
        "UTC":  0*60*60,
        "EST": -5*60*60,
        "CST": -6*60*60,
        "MST": -7*60*60,
        "PST": -8*60*60,
    }
    offset := timeZone["EST"]
    log.Println("time zone", offset)
    */
    /*
    ts := "ts"
    if seconds, ok := timeZone[ts]; ok {
        log.Println("time zone", tz)
    }
    */
}
func getmd5(data string) string {
    hash := md5.New( )
    hash.Write([]byte(data))
    return fmt.Sprintf("%x", hash.Sum())
}

func dirExists(dir string) bool {
    d, e := os.Stat(dir)
    switch {
        case e != nil:
            return false
        case !d.IsDirectory():
            return false
    }
    return true
}

func fileExists(dir string) bool {
    info, err := os.Stat(dir)
    if err != nil {
        return false
    } else if !info.IsRegular() {
        return false
    }
    return true
}

