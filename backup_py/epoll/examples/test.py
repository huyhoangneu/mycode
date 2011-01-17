import socket, logging  
import select, errno

epoll_fd = select.epoll()
