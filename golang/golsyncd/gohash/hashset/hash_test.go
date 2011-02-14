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
package hashset

import "testing"

type StringHasher string

func (sh StringHasher) Hashcode() (hc uint64) {
	for i, c := range sh {
		hc += uint64(c) * 2 << uint64(i)
	}
	return
}

func (sh StringHasher) Equals(other interface{}) bool {
	if s, ok := other.(StringHasher); ok {
		return s == sh
	}
	return false
}

func TestSet(t *testing.T) {
	hs := New()
	hs.Insert(StringHasher("hello, world!"))
	hs.Insert(StringHasher("hello, there!"))
	hs.Insert(StringHasher("this is a sentence."))
	if !hs.Contains(StringHasher("hello, world!")) {
		t.Fail()
	}
	if !hs.Contains(StringHasher("hello, there!")) {
		t.Fail()
	}
	if !hs.Contains(StringHasher("this is a sentence.")) {
		t.Fail()
	}
	if hs.Contains(StringHasher("something else")) {
		t.Fail()
	}
	if hs.Size() != 3 {
		t.Fail()
	}
	hs.Insert(StringHasher("hello, world!"))
	if hs.Size() != 3 {
		t.Fail()
	}
	hs.Remove(StringHasher("hello, there!"))
	if hs.Contains(StringHasher("hello, there!")) {
		t.Fail()
	}
	if hs.Size() != 2 {
		t.Fail()
	}
}
