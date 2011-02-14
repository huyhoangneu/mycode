package golsyncd 
import "os/inotify"
import "os"
import "crypto/md5"
import "log"
import "sync"
import "fmt"
const (
	DEBUG int = 0
)
type Golsyncd struct {
	Logging   bool
	mutex       *sync.Mutex
}
func (golsyncd *Golsyncd) DebugStart(level bool) {
	if level {
	golsyncd.Logging = true
	} else {
	}
}
func New() (golsyncd *Golsyncd) {
	// Create and return a new instance of MySQL
	golsyncd = new(Golsyncd)
	// Setup mutex
	golsyncd.mutex = new(sync.Mutex)
	return
}
func (golsyncd *Golsyncd) Start() {
	watcher, err := inotify.NewWatcher()
	if err != nil {
    		log.Print(err)
	}
	err = watcher.Watch("/tmp")
	watcher.AddWatch("/tmp/a", inotify.IN_ALL_EVENTS)
	if err != nil {
    		log.Print(err)
	}
	for {
		select {
			case ev := <-watcher.Event:
                //delete file
                if ev.Mask == inotify.IN_DELETE {
                    log.Println("Delete file name:", ev.Name)
                }
                if ev.Mask == inotify.IN_CREATE|inotify.IN_ISDIR {
                    watcher.AddWatch(ev.Name, inotify.IN_ALL_EVENTS)
                    log.Println("create dir:", ev.Name)
                }
                if ev.Mask == inotify.IN_DELETE|inotify.IN_ISDIR {
                    if err := watcher.RemoveWatch(ev.Name); err != nil {
                        log.Println("can't remove non-existent inotify watch for: ", ev.Name)
                        log.Println("remove err:", err)
                    }
                    log.Println("del dir:", ev.Name)
                }
                if golsyncd.Logging {
				    log.Println("event name:", ev.Name)
				    log.Println("event mask:", ev.Mask)
				    log.Println("event cookie:", ev.Cookie)
				    log.Println("event:", ev)
				    log.Println("event:", ev.String())
                }
			case err := <-watcher.Error:
				  log.Println("error:", err)
		}
	}
}

func getmd5(data string) string {
    hash := md5.New( )
    hash.Write([]byte(data))
    return fmt.Sprintf("%x", hash.Sum())
}

func dirExists(dir string) bool {
    d, e := os.Stat(dir)
    switch {
        case e != nil:
            return false
        case !d.IsDirectory():
            return false
    }
    return true
}

func fileExists(dir string) bool {
    info, err := os.Stat(dir)
    if err != nil {
        return false
    } else if !info.IsRegular() {
        return false
    }
    return true
}

