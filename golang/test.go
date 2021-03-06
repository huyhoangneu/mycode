package main

import ("./file"
        "fmt"
        "os"
        "flag"
)
/*
func main() { 
    hello := []byte{'h', 'e', 'l', 'l', 'o', ',', ' ', 'w', 'o', 'r', 'l', 'd', '\n'} 
    file.Stdout.Write(hello) 
    file, err := file.Open("/does/not/exist", 0, 0) 
    if file == nil { 
        fmt.Printf("can't open file; err=%s\n", err.String()) 
        os.Exit(1) 
    } 
}
*/
func cat(f *file.File) { 
    const NBUF = 512 
    var buf [NBUF]byte 
    for { 
        switch nr, er := f.Read(buf[:]); true { 
            case nr < 0: 
                fmt.Fprintf(os.Stderr, "cat: error reading from %s: %s\n", f.String(), er.String()) 
                os.Exit(1) 
            case nr == 0: // EOF 
                return 
            case nr > 0: 
                if nw, ew := file.Stdout.Write(buf[0:nr]); nw != nr { 
                    fmt.Fprintf(os.Stderr, "cat: error writing from %s: %s\n", f.String(), ew.String()) 
                } 
        } 
    } 
}

func main() { 
    flag.Parse() // Scans the arg list and sets up flags 
    if flag.NArg() == 0 { 
        cat(file.Stdin) 
    } 
    for i := 0; i < flag.NArg(); i++ { 
        f, err := file.Open(flag.Arg(i), 0, 0) 
        if f == nil { 
            fmt.Fprintf(os.Stderr, "cat: can't open %s: error %s\n", flag.Arg(i), err) 
            os.Exit(1) 
        } 
        cat(f) 
        f.Close() 
    } 
}
/*
func main() {
    file := &file.File{1, "abc"}
    fmt.Printf("%v\n", file)
}
*/
