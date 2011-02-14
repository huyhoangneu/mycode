/*
Copyright © 2010 John Asmuth. All Rights Reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list
of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this
list of conditions and the following disclaimer in the documentation and/or other
materials provided with the distribution.

3. The name of the author may not be used to endorse or promote products derived
from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY [LICENSOR] "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF
USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.
*/

//target:gohash.googlecode.com/hg/hashlessset
package hashlessset

import (
	"github.com/petar/GoLLRB/llrb"
)

type HasherLess interface {
	Hashcode() uint64
	LessThan(other interface{}) bool
}
func LessFunc(a, b interface{}) bool {
	return a.(HasherLess).LessThan(b)
}

type Set struct {
	bins map[uint64]*llrb.Tree
	count int
}

func New() (this *Set) {
	this = new(Set)
	this.bins = make(map[uint64]*llrb.Tree)
	return
}

func (this *Set) Keys() (out chan HasherLess) {
	out = make(chan HasherLess)
	go func(out chan HasherLess) {
		for _, bin := range this.bins {
			for item := range bin.IterAscend() {
				out <- item.(HasherLess)
			}
		}
	}(out)
	return
}

func (this *Set) Insert(hl HasherLess) {
	bin := this.bins[hl.Hashcode()]
	if bin == nil {
		bin = llrb.New(LessFunc)
		//bin.Init()
		this.bins[hl.Hashcode()] = bin
	}
	if bin.ReplaceOrInsert(hl) == nil {
		this.count++
	}
}

func (this *Set) Remove(hl HasherLess) {
	bin := this.bins[hl.Hashcode()]
	if bin == nil {
		return
	}
	if bin.Delete(hl) != nil {
		this.count--
	}
}

func (this *Set) Get(hl HasherLess) (item HasherLess, ok bool) {
	bin := this.bins[hl.Hashcode()]
	if bin == nil {
		return
	}
	itemi := bin.Get(hl)
	ok = itemi != nil
	if ok {
		item = itemi.(HasherLess)
	}
	return
}

func (this *Set) Contains(hl HasherLess) bool {
	bin := this.bins[hl.Hashcode()]
	if bin == nil {
		return false
	}
	return bin.Has(hl)
}

func (this *Set) Size() int {
	return this.count
}
