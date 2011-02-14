#include $(GOROOT)/src/Make.inc
#
#TARG=testfile
#GOFILES=tf.go file.go
#
#include $(GOROOT)/src/Make.pkg 


include $(GOROOT)/src/Make.inc
all:
	$(GC) file.go
	$(GC) test.go
	$(LD) -o test.out test.$O

clean:
	rm -rf *.6 *.8 *.o *.out

