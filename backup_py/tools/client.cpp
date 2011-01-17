#include <signal.h>
#include <fcntl.h>
#include <unistd.h>
#include <poll.h>
#include <pthread.h>
#include <errno.h>
#include <getopt.h>
#include <assert.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <cstring>
#include <cstdlib>
#include <cstdio>
#include <string>
using namespace std;

#ifndef u_char 
typedef unsigned char u_char;
#endif

#ifndef u_short
typedef unsigned short u_short;
#endif

#ifndef u_int
typedef unsigned int u_int;
#endif

struct addr_t{
public:
	addr_t(void):_addr(""), _ip(0), _port(0){}
	addr_t(string &add, u_int ip, u_short p){
		_addr = add;
		_ip = ip;
		_port = p;
	}

	string	_addr;
	u_int	_ip;
	u_short	_port;
};

struct attr{
#define TCP_SOCKET	0x01
#define	UDP_SOCKET	0x02
	u_char	_st;

#define BT_PROTOCOL		0x01
#define	FTSP_PROTOCOL	0x02
	u_char	_pt;

#define DEFAULT_THREAD_NUM		4
	u_short	_tn;

#define DEFAULT_PARALLEL_NUM	5000
	u_short	_pn;

	u_short	_detail;
	u_short	_tc;
};

struct tcounter{
	u_int	_tid;
	u_int	_tc;
	u_int	_tr;
	u_int	_te;
	u_short	_pn;
	bool	_stop;
};

static attr		g_attr;
static addr_t	g_addr;
u_int			g_enable_counter;


void		parse_command_line(int argc, char* argv[]);
void *		tcp_client(void *);
void *		udp_client(void *);
void		sig_handler(int signo);
tcounter *	get_counter(u_int n=1);
int			set_nonblock(int fd);


int 
main(int argc, char *argv[])
{
	parse_command_line(argc, argv);

	if (g_enable_counter) {
		signal(SIGALRM, sig_handler);
		alarm(g_enable_counter);
	}

	tcounter *plist = get_counter(g_attr._tn);
	for(u_int i=0; i<g_attr._tn; i++) {
		plist[i]._pn = g_attr._pn;
	}

	void *(*pfunc)(void*) = tcp_client;
	if ( g_attr._st == UDP_SOCKET) {
		pfunc = udp_client;
	}

	for (u_int i=0; i<g_attr._tn; i++) {
		pthread_t pid;
		if (pthread_create(&pid, NULL, pfunc, plist+i) != 0) {
			printf("failed create thread\n");
			exit(0);
		}
	}

	while (true)
		sleep((unsigned int)-1);

}

void 
usage(void) {
	printf("uage: client.out\n");
	printf("--port     						server port\n");
	printf("--ip        					server ip\n");
	printf("--tcp							tcp connection\n");
	printf("--udp							udp connection\n");
	printf("--bt							bt protocol\n");
	printf("--ftsp							ftsp protocol\n");
	printf("--thread						thread number\n");
	printf("--enable_counter				display the total number of request\n");
	printf("--parallel						max parallel connection client holding\n");
	printf("--transaction_detail			display transaction detail\n");
	printf("--count							total number of request\n");
	printf("--help							print usage\n\n");
	exit(0);
}

void 
parse_command_line(int argc, char* argv[])
{
	if(argc == 1) { usage(); }
	char opt;
	addr_t addr;
	g_enable_counter = 0;
	while(1){
		static struct option opts[] = { 
			{"port",		1, 0, 'p'},
			{"ip",			1, 0, 'i'},
			{"tcp",			0, 0, 'T'},
			{"udp",			0, 0, 'U'},
			{"bt",          0, 0, 'b'},
			{"ftsp",		0, 0, 'f'},
			{"thread",		1, 0, 't'},
			{"parallel",	1, 0, 'P'},
			{"enable--counter",		1, 0, 'E'},
			{"transaction_detail",	0, 0, 'D'},
			{"count",		1, 0, 'C'},
			{"help",        0, 0, 'h'},
			{0,0,0,0}
		};  
		opt = getopt_long(argc, argv, "i:p:TUbft:P:E:DCh", opts, NULL);
		if(opt == -1) { break; }

		switch(opt){
		case 'h':  usage();								break;
		case 'i':  addr._addr.assign(optarg);			break;
		case 'p':  addr._port = atoi(optarg);			break;
		case 'T':  g_attr._st = TCP_SOCKET;				break;
		case 'U':  g_attr._st = UDP_SOCKET;				break;
		case 'b':  g_attr._pt = BT_PROTOCOL;			break;
		case 'f':  g_attr._pt = FTSP_PROTOCOL;			break;
		case 't':  g_attr._tn = (u_int)atoi(optarg);	break;
		case 'P':  g_attr._pn = (u_int)atoi(optarg);	break;
		case 'D':  g_attr._detail = 1;					break;
		case 'C':  g_attr._tc = (u_int)atoi(optarg);	break;
		case 'E':  g_enable_counter = (u_int)atoi(optarg); break;
		default:   usage();   							break;
		}   
	}

	g_addr._addr = addr._addr;
	if ((g_addr._ip = inet_addr(addr._addr.c_str())) == (u_int)-1)
	{
		printf("error: invalid server ip: %s\n\n", g_addr._addr.c_str());
		usage();
	}
	g_addr._port = htons(addr._port);

	if (g_attr._pt == BT_PROTOCOL)
	{
		if (g_attr._st != TCP_SOCKET)
		{
			printf("--bt args must use with --tcp or -T args\n");
			usage();
		}
	}

	if (g_attr._tc)
	{
	}

	printf("starting test remote server: %s:%d\n\n", addr._addr.c_str(), addr._port);
}

static void 
rand_taskid(char *buf, int len)
{
	assert(buf && len == 20);

	srand(time(NULL));
	u_int *np = (u_int *)buf;

	np[0] = rand();
	np[1] = rand();
	np[2] = rand();
	np[3] = rand();
	np[4] = rand();
}

static void 
init_ftsp_request(char * buf, int len)
{
	assert( buf && len>=106 );

	*((u_int *)(buf+4)) = htonl(108);
	*((u_short *)(buf+8)) = htons(0x0001);
	*((u_short *)(buf+10)) = htons(0x0001);
	*((u_short *)(buf+12)); /* session id */
	*(buf+14) = 0x01;
	*((u_int *)(buf+15)) = htonl(0x01020304);
	*(buf+19) = 0x01;
	*(buf+20) = 0x01;
	*(buf+21) = 0x01;
	*(buf+22) = 0x01;
	*(buf+23) = 0x01;
	*((u_int *)(buf+24)) = htonl(0x01020304);
	*((u_short *)(buf+28)) = htons(8000);
	*((u_int *)(buf+30)) = htonl(0x04030201);
	*((u_short *)(buf+34)) = htons(8080);
	*((u_short *)(buf+36)) = htons(8001);
	*((u_short *)(buf+38)); /* upload rate */
	*((u_short *)(buf+40)); /* download rate */

	*((u_int *)(buf+42)); 
	*((u_int *)(buf+46)); /* upload total flow */

	*((u_int *)(buf+50)) = htonl(0x04030201);
	*((u_int *)(buf+54)) = htonl(0x04030201); /* download total flow */

	*((u_int *)(buf+58)) = htonl(0x04030201);
	*((u_int *)(buf+62)) = htonl(0x04030201); /* left total flow */

	*((u_short *)(buf+66)) = htons(8001); /* play position */

	char *p = buf + 88; /* peer id */
	for (int i=0; i<20; i++) {
		*(p+i) = 'x';
	}
	rand_taskid(buf + 68, 20);	/* task id */
}

int get_hex_char_val(const char c, char &val)
{
	if(c>='0' && c<='9')
	{
		val = c - '0';	
	}
	else if(c>='a' && c<='f')
	{
		val = c - 'a' + 10;
	}
	else if(c>='A' && c<='F')
	{
		val = c - 'A' + 10;
	}
	else
	{
		return -1;
	}

	return 0;
}

static const char HEX_STR[] = "0123456789ABCDEF";

int byte2hexstr(const char *str, int len, string &res)
{
	res = "";
	const char *pchar = str;
	for(int i=0; i<len; i++)
	{
		res.append(1, HEX_STR[(*pchar & 0xF0)>>4]);
		res.append(1, HEX_STR[*pchar & 0x0F]);
		pchar++;
	}

	return 0;
}

static int init_bt_request(char *buf, int len)
{
    /*const string format = "GET http://125.32.112.44/info.php HTTP/1.0\r\n" \*/
	/*const string format = "GET http://w.1616.net/www/clock.php?call=TIMEBACK HTTP/1.0\r\n" \*/
    const string format = "GET http://apps.1616.net/favorite/index.php HTTP/1.0\r\n" \
		"User-Agent: Mozilla/4.0\r\n"			\
		"Connection: close\r\n"					\
		"Accept-Encoding: gzip, deflate\r\n"	\
		"Host: ls.funshion.com:8080\r\n"		\
		"Cache-Control: no-cache\r\n\r\n";

	assert(buf && len>=(format.length()+80));

	char tid[20];
	rand_taskid(tid, 20);
	string strtid;
	byte2hexstr(tid, 20, strtid);

	return snprintf(buf, len, format.c_str(), strtid.c_str(), "XXXXXXXXXXXXXXXXXXXX");
}
/*
struct pollfd {
int fd;        
short events;  
short revents; 
};
*/
static void 
init_sockaddr(sockaddr_in &rhost)
{
	memset(&rhost, 0, sizeof(rhost));

	rhost.sin_family=AF_INET;	
	rhost.sin_port = g_addr._port;
	rhost.sin_addr.s_addr = g_addr._ip;
}

static int
connect_server(int fd, sockaddr_in &rhost)
{
	int ret = ::connect(fd, (struct sockaddr *)&rhost, sizeof(rhost));
	if (ret == -1 && errno == EINPROGRESS)
		ret = 0;
	
	return ret;
}

static void 
init_polllist(pollfd *pl, int n)
{
	assert(pl && n>0);
	
	memset(pl, 0, sizeof(pollfd)*n);

	sockaddr_in rhost;
	init_sockaddr(rhost);
	static int x = 0;
	for (int i=0; i<n; i++) {
		pl[i].fd = socket(AF_INET, SOCK_STREAM, 0);
		if (pl[i].fd == -1) {
			printf("failed create tcp socket, errno: %u, info: %s\n", errno, strerror(errno));
			exit(1);
		}
		set_nonblock(pl[i].fd);

		if (connect_server(pl[i].fd, rhost) !=0) {
			printf("%d \t", x++);
			printf("failed connect to remote server, errno: %u, info: %s\n", errno, strerror(errno));
			exit(1);
		}
		pl[i].events = POLLOUT ;
	}
}

static int
create_new_connected_tcpsock(sockaddr_in &rhost)
{
	int fd = socket(AF_INET, SOCK_STREAM, 0);
	if (fd == -1) {
		printf("failed create tcp socket, errno: %u, info: %s\n", errno, strerror(errno));
		exit(1);
	}
	set_nonblock(fd);

	if (connect_server(fd, rhost) !=0) {
		printf("failed connect to remote server, errno: %u, info: %s\n", errno, strerror(errno));
		exit(1);
	}

	return fd;
}

void * 
tcp_client(void * arg)
{
	printf("start tcp client\n");
	tcounter *pcounter = (tcounter *)arg;
	
	u_short pn = pcounter->_pn;

	struct pollfd *fds = new pollfd[pn];

	init_polllist(fds, pn);

	char rb[512];
	char wb[1024];
	int len = 108;
	init_ftsp_request(wb, len);

	sockaddr_in rhost;
	init_sockaddr(rhost);

	while (true) {
		int n = poll(fds, pn, 0);
		//printf("%d\n", n);
		if (n > 0) {
			for (int i=0; i<pn && n>0; i++) {
				if (fds[i].revents & POLLOUT) {
					n--;
					if (g_attr._pt == BT_PROTOCOL)
						len = init_bt_request(wb, 1024);

					int nsnd = send(fds[i].fd, wb, len, 0);
					if (nsnd > 0) {
						fds[i].events = POLLIN;
						continue;
					}

					if ((nsnd < 0 && (errno == EWOULDBLOCK || errno == EAGAIN || errno == EINTR))){
						continue;
					}

					printf("failed send data to remote server errno: %d, info: %s\n", errno, strerror(errno));
					close(fds[i].fd);
					
					/* create a new one */
					fds[i].fd = create_new_connected_tcpsock(rhost);
					fds[i].events = POLLOUT;
				}
				
				if (fds[i].revents & POLLIN) {
					n--;
					int nsnd = recv(fds[i].fd, rb, 512, 0);
					if (nsnd < 0 && (errno == EWOULDBLOCK || errno == EAGAIN || errno == EINTR)){
						continue;
					}
					else if (nsnd > 0)
						continue;
					else if (nsnd < 0)
						pcounter->_te += 1;
					else 
						pcounter->_tc += 1;

					close(fds[i].fd);

					fds[i].fd = create_new_connected_tcpsock(rhost);
					fds[i].events = POLLOUT;
				}

				if (fds[i].revents & POLLERR || fds[i].revents & POLLHUP) {
					pcounter->_te += 1;
					close(fds[i].fd);
					
					fds[i].fd = create_new_connected_tcpsock(rhost);
					fds[i].events = POLLOUT;
				}
			}
		}
	}

	return NULL;
}


void * 
udp_client(void * arg)
{
	tcounter *pcounter = (tcounter *)arg;
	
	printf("start udp client\n");
	int fd = socket(AF_INET, SOCK_DGRAM, 0);
	if (fd == -1)
		return NULL;

	set_nonblock(fd);

	int addrlen = sizeof(struct sockaddr_in);
	sockaddr_in		rhost;

	init_sockaddr(rhost);

	u_short pn = pcounter->_pn;
	char rb[512];
	char wb[512];
	const int len = 108;

	init_ftsp_request(wb, len);

	while (true) {
		for (u_short i=0; i<pn; i++) {
			int n = sendto(fd, wb, len, 0, (struct sockaddr*)&rhost, addrlen);
			if (n > 0) {
				pcounter->_tc += 1;

				rand_taskid(wb+66, 20);
			}
			else if (n < 0 && (errno == EWOULDBLOCK || errno == EAGAIN || errno == EINTR))
				break;
			else {
				printf("error: socket send error id: %u, info: %s\n", errno, strerror(errno));
				pcounter->_te += 1;
			}

		}

		while (true) {
			int n = recvfrom(fd, rb, 512, 0, NULL, 0);
			if ( n>0 )
			{
				pcounter->_tr += 1;
				continue;
			}

			else if (n < 0 && (errno == EWOULDBLOCK || errno == EAGAIN || errno == EINTR))
				break;
			else {
				printf("error: socket recv error id: %u, info: %s\n", errno, strerror(errno));
				exit(1);
			}
		}	
	}
}

void 
sig_handler(int signo)
{
	static u_int nl=0, lte=0, ltr=0;

	u_int	nc=0;
	u_int	te=0;
	u_int 	tr=0;
	tcounter *p = get_counter(g_attr._tn);
	for (u_int i=0; i<g_attr._tn; i++) {
		nc += (p+i)->_tc;
		te += (p+i)->_te;
		tr += (p+i)->_tr;
	}

	if (g_attr._st == TCP_SOCKET)
		printf("sended: %u/%us\t\ttotal finished: %u\t\terror: %u/%us\t\ttotal error: %u\n", nc-nl, g_enable_counter, nc, te-lte, g_enable_counter, te);
	else 
		printf("snd: %u/%us\t\tt_snd: %u\t\trecv: %u/%us\t\tt_recv: %u\t\terror: %u/%us\t\tt_error: %u\n", 
						nc-nl, g_enable_counter, nc, tr-ltr, g_enable_counter, tr, te-lte, g_enable_counter, te);

	nl = nc;
	lte = te;
	ltr = tr;

	alarm(g_enable_counter);
}

tcounter *
get_counter(u_int n/*=1*/)
{
	static tcounter * plist = NULL;
	if (plist == NULL) {
		plist = new tcounter[n];
		memset(plist, 0, sizeof(tcounter)*n);
	}
	return plist;
}

int 
set_nonblock(int fd)
{
	int ret = 0;
#ifdef _WIN32
	u_long argp = 1;
	ret = ioctlsocket(fd, FIONBIO, &argp);
	if ( ret == SOCKET_ERROR )  
		ret = SOCKET_NONBLOCK_ERROR;
#else
	int flags = fcntl(fd, F_GETFL, 0);
	ret = fcntl(fd, F_SETFL, O_NONBLOCK | flags);
#endif
	return 0;
}
