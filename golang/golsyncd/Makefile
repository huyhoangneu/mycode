include $(GOROOT)/src/Make.inc
all:
	$(GC) hashset.go
	$(GC) hashmap.go
	$(GC) golsyncd.go
	
	$(GC) test.go
	$(LD) -o test.out test.$O
	
	$(GC) examples_1.go
	$(LD) -o examples_1.out examples_1.$O

clean:
	rm -rf *.6 *.8 *.o *.out
