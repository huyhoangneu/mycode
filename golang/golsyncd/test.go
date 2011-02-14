package main
//import "os/inotify"
import "./golsyncd"
//import "log"
func main() {
    sync := golsyncd.New()
    sync.Logging = true
    //sync.Logging = false
    sync.Start( )
    /*
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
				 log.Println("event name:", ev.Name)
				 log.Println("event mask:", ev.Mask)
				 log.Println("event cookie:", ev.Cookie)
				 log.Println("event:", ev)
				 log.Println("event:", ev.String())
			case err := <-watcher.Error:
				  log.Println("error:", err)
		}
	}
    */
}
