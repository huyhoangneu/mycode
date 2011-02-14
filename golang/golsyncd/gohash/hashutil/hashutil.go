//target:gohash.googlecode.com/hg/hashutil
package hashutil

import "unsafe"

func HashFloat64(f float64) (hash uint64) {
	hash = *(*uint64)(unsafe.Pointer(&f))
	return
}
func HashFloat64Slice(fs []float64) (hash uint64) {
	for i, f := range fs {
		hf := HashFloat64(f)
		hash += hf << uint(i)
		hash += hf >> uint(64-i)
	}
	return
}

type Float64SliceHasher []float64
func (this Float64SliceHasher) Hashcode() (hash uint64) {
	return HashFloat64Slice(this)
}
func (this Float64SliceHasher) Compare(other Float64SliceHasher) int {
	for i, v := range this {
		if v < other[i] {
			return -1
		}
		if v > other[i] {
			return 1
		}
	}
	return 0
}
func (this Float64SliceHasher) LessThan(oi interface{}) bool {
	return this.Compare(oi.(Float64SliceHasher)) < 0
}
func (this Float64SliceHasher) Equals(oi interface{}) bool {
	return this.Compare(oi.(Float64SliceHasher)) == 0
}