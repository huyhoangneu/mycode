// ------------------------
// Aggregate keys  example
// ------------------------




package main

import (
	"strconv"
	"gohash.googlecode.com/hg/hashmap"
	"fmt"
)


// Aggregate structure which will be used as key
// It does not need to have a particular name, it simply has to
// be given the two methods :

// - Hashcode()
// - Equals()

type Hasher struct {
    name string
    age  int
}


// Hashcode() method for the aggregate key : Hasher, above
func (sh Hasher) Hashcode() (hc uint64) {
	identifier := sh.name + " " + strconv.Itoa(sh.age)
    for i, c := range identifier {
		hc += uint64(c) * 2 << uint64(i)
	}
	return
}


// Equals() method for the aggregate key : Hasher, above
func (sh Hasher) Equals (other interface{}) bool {
    return (sh.name == (other.(Hasher)).name) && (sh.age == (other.(Hasher)).age)
}

func main() {

    // Creating a hashmap
    hm := hashmap.New()

    //checking initial size (should be zero)
    s := hm.Size()
	fmt.Printf("s = %v\n", s)

	println("---")

    // Inserting objects (structs in this case)
	for i := 0; i < 3; i++ {
		hm.Put(Hasher{"john_"+strconv.Itoa(i), i}, "A_"+strconv.Itoa(i))
	}

    // Checking that no duplicate object gets inserted in the Hashmap 
    hm.Put(Hasher{"john_"+strconv.Itoa(0), 0}, "A_"+strconv.Itoa(0))

	if grade, ok := hm.Get(Hasher{"john_2", 2}); ok {
		fmt.Printf("value = %v\n", grade)
	}

	println("---")

    // Checking the size of he container after insertion (should be 3, now)
    s = hm.Size()
	fmt.Printf("s = %v\n", s)

	println("---")

    // Listing the Keys of the Hashmap, using range expression
    for  key := range hm.Keys() {
		fmt.Printf("Keys = %v\n", key)
    }

	println("---")

    // Listing the Keys and value (map-style), using the range expression 
    for  item := range hm.KeyValues() {
		fmt.Printf("Key = %v, Value = %v \n", item.Key, item.Value)
    }

}
