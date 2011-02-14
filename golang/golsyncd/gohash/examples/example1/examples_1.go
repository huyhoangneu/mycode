// ----------------------------------------
// Simple example : Storing strings as keys
// (as in the case of a built-in Go map)
// ----------------------------------------


package main

import (
	"strconv"
	"gohash.googlecode.com/hg/hashmap"
	"fmt"
)

type StringHasher string

// Hashcode() method for the key 
func (sh StringHasher) Hashcode() (hc uint64) {
	for i, c := range sh {
		hc += uint64(c) * 2 << uint64(i)
	}
	return
}


// Equals() method for the key 
func (sh StringHasher) Equals (other interface{}) bool {
        return sh == other.(StringHasher)
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
		hm.Put(StringHasher("john_"+strconv.Itoa(i)), "A_"+strconv.Itoa(i))
	}


    // Checking that no duplicate object gets inserted in the Hashmap 
    hm.Put(StringHasher("john_"+strconv.Itoa(0)), "A_"+strconv.Itoa(0))

	if grade, ok := hm.Get(StringHasher("john_2")); ok {
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

    // Listing the Keys and values (map-style), using the range expression 
    for  item := range hm.KeyValues() {
		fmt.Printf("Key = %v, Value = %v \n", item.Key, item.Value)
    }

}
