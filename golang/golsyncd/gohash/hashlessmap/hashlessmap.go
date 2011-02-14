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

//target:gohash.googlecode.com/hg/hashlessmap
package hashlessmap

import (
	"gohash.googlecode.com/hg/hashlessset"
)

type HasherLess hashlessset.HasherLess
type Map hashlessset.Set

type KeyValue struct {
	Key HasherLess
	Value interface{}
}

func (kv KeyValue) Hashcode() uint64 {
	return kv.Key.Hashcode()
}

func (kv KeyValue) LessThan(other interface{}) bool {
	okv := other.(KeyValue)
	return kv.Key.LessThan(okv.Key)
}

func New() (hs *Map) {
	hs = (*Map)(hashlessset.New())
	return
}

func (hs *Map) Keys() (out chan interface{}) {
	out = make(chan interface{})
	go func(out chan interface{}) {
		for kv := range hs.KeyValues() {
			out <- kv.Key
		}
	}(out)
	return
}

func (hs *Map) Values() (out chan interface{}) {
	out = make(chan interface{})
	go func(out chan interface{}) {
		for kv := range hs.KeyValues() {
			out <- kv.Value
		}
	}(out)
	return
}

func (hs *Map) KeyValues() (out chan KeyValue) {
	out = make(chan KeyValue)
	go func(out chan KeyValue) {
		for kvi := range (*hashlessset.Set)(hs).Keys() {
			out <- kvi.(KeyValue)
		}
	}(out)
	return
}

func (hs *Map) Put(h HasherLess, v interface{}) {
	kv := KeyValue{h, v}
	(*hashlessset.Set)(hs).Insert(kv)
}

func (hs *Map) Get(h HasherLess) (value interface{}, ok bool) {
	kvi, ok := (*hashlessset.Set)(hs).Get(KeyValue{h, nil})
	if ok {
		value = (kvi.(KeyValue)).Value
	}
	return
}
