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

//target:gohash.googlecode.com/hg/hashmap
package hashmap

import (
	"gohash.googlecode.com/hg/hashset"
)

type Hasher hashset.Hasher
type Map hashset.Set

type KeyValue struct {
	Key Hasher
	Value interface{}
}

func (kv KeyValue) Hashcode() uint64 {
	return kv.Key.Hashcode()
}

func (kv KeyValue) Equals(other interface{}) bool {
	okv := other.(KeyValue)
	return kv.Key.Equals(okv.Key)
}

func New() (hs *Map) {
	hs = (*Map)(hashset.New())
	return
}

func (hs *Map) Size() int {
    return  hs.Count
}

func (hs *Map) Keys() (out chan interface{}) {
	out = make(chan interface{})
	go func(out chan interface{}) {
		for kv := range hs.KeyValues() {
			out <- kv.Key
		}
        close(out)
	}(out)
	return
}

func (hs *Map) Values() (out chan interface{}) {
	out = make(chan interface{})
	go func(out chan interface{}) {
		for kv := range hs.KeyValues() {
			out <- kv.Value
		}
        close(out)
	}(out)
	return
}

func (hs *Map) KeyValues() (out chan KeyValue) {
	out = make(chan KeyValue)
	go func(out chan KeyValue) {
		for kvi := range (*hashset.Set)(hs).Keys() {
			out <- kvi.(KeyValue)
		}
        close(out)
	}(out)
	return
}

func (hs *Map) Put(h Hasher, v interface{}) {
	kv := KeyValue{h, v}
	(*hashset.Set)(hs).Insert(kv)
}

func (hs *Map) Get(h Hasher) (value interface{}, ok bool) {
	kvi, ok := (*hashset.Set)(hs).Get(KeyValue{h, nil})
	if ok {
		value = (kvi.(KeyValue)).Value
	}
	return
}
