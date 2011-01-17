#!/usr/bin/python
# -*- coding: UTF-8 -*-
import smtplib,string,sys
from smtplib import SMTPAuthenticationError
from smtplib import SMTP
from email.base64MIME import encode as encode_base64
from email.MIMEText import MIMEText

class SendMail:
    def __init__(self, smtpServer, user = '', passwd = ''):
        self.smtpServer = smtpServer
        self.user = user
        self.passwd = passwd
    def get_smtp_client(self):
        server=smtplib.SMTP(self.smtpServer)
        if len(self.user)!=0 and len(self.passwd)!=0:
            #这里需要处理 标识 完整的 客户端
            server.ehlo()
            server.starttls()
            server.ehlo()
            try:
                server.login(self.user, self.passwd)
            except SMTPAuthenticationError:
                sys.stdout.write('\n------- try Auth Login again ------\n')
                server = smtplib.SMTP(self.smtpServer)
                debuglevel = '1'
                if debuglevel:
                    server.set_debuglevel(True)
                server.ehlo()
                (code, resp) = server.docmd('AUTH LOGIN')
                if code != 334:
                    raise SMTPAuthenticationError(code, resp)
                (code, resp) = server.docmd(encode_base64(self.user, eol=""))
                if code != 334:#503:#334:
                    raise SMTPAuthenticationError(code, resp)
                (code, resp) = server.docmd(encode_base64(self.passwd, eol=""))
                if code != 235:
                    raise SMTPAuthenticationError(code, resp)
        return server
    def send_mail(self, server, send_sub, send_to, subject, send_content):
        #send_sub 发送人 邮箱
        #send_to 发送至
        #subject 主题
        #send_content 发送内容
        b = '''<html><head>
         <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
         </head><body><pre>'''
        b += send_content 
        b += '''</pre></body></html>'''
        msg = MIMEText(b, _subtype = 'html', _charset = 'utf-8')
        msg['Subject'] = subject
        msg['From'] = send_sub 
        msg['To'] = send_to
        msg.as_string()
        #body=string.join(("FROM: %s" % fromAddr, "TO: %s" % toAddr, "Subject: %s" % subject, "", msg),"\r\n")
        server.sendmail(send_sub, send_to, msg.as_string())
        #server.quit()
sendmail = SendMail
if __name__ == "__main__":
    #smDemo=SendMail("smtp.qq.com","136030112","**JINZHIJIE")
    smDemo=SendMail("mail.kaixin43.com","liuyan","baby10281228")
    #smDemo=SendMail("211.100.36.228","mailer","1616@myhome")
    s = smDemo.get_smtp_client()
    #smDemo.send_mail(s, "seacoastboy@gmail.com","seacoastboy@sogou.com","SendMailTest","Just Test")
    smDemo.send_mail(s, "136030112@qq.com","seacoastboy@gmail.com","明天过节了","就是 明天真的就过节了！")
    s.close()
