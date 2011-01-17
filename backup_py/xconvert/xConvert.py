#!/usr/bin/env python
# -*- coding:utf-8 -*-
import os,commands,sys,math,time
from xLogging import xLogging
class xConvert:
    __im_script = {
        "resize"    : 'convert %s +profile "*" -crop %dx%d+%d+%d +repage -resize %dx%d! %s',
        "scale"     : 'convert %s  +profile "*" -resize "%sx%s>" %s',
        "identify"  : "identify -format '%%w %%h,' -quiet  %s",
        "image_info": "identify -format '%%w %%h %%r ,' %s",
        "identify2" : "identify -format '%%w %%h' -quiet  %s",
        "coalesce"  : "convert %s +profile \"*\" -coalesce  %s",
        "transcript": "convert %s %s",
        "watermark" : "convert -quality 90 -sharpen 1 -size %s xc:black -draw \"image over 0,0 %s '%s'\" %s %s",#-draw \"line 0,%d %d,%d\" -gravity SouthWest
        "draw"      : " -draw \"image over %d,%d %d,%d '%s'\" %s ",
        'export'    : "convert %s -quiet -quality 90 %s",
        "waterMark2": "composite -watermark %s %s %s %s %s",
        "colorspace": "mogrify -colorspace RGB -quality 100 %s",
        "color"     : "identify -format \"%%r \" %s"
    }

    __gm_script = {
        "resize"    : 'gm convert %s +profile "*" -crop %dx%d+%d+%d -resize %dx%d! %s',
        "scale"     : 'gm convert %s  +profile "*" -resize "%sx%s>" %s',
        "identify"  : "gm identify -format '%%w %%h,' %s",
        "image_info": "gm identify -format '%%w %%h %%r,' %s",
        "identify2" : "gm identify -format '%%w %%h' %s",
        "coalesce"  : "gm convert %s +profile \"*\" -coalesce  %s",
        "transcript": "gm convert %s %s",
        "watermark" : "gm convert -quality 90 -sharpen 1 -size %s xc:black -draw \"image over 0,0 %s '%s'\" %s %s",
        "draw"      : " -draw \"image over %d,%d %d,%d '%s'\" %s ",
        'export'    : "gm convert %s -quality 90 %s",
        "waterMark2": "gm composite -watermark %s %s %s %s %s",
        "colorspace": "gm mogrify -colorspace RGB -quality 100 %s",
        "color"     : "gm identify -format \"%%r \" %s"
    }
    
    __logo_script = {
        "logo" : ['/Data/shimao/script/XLib/logo/logo.png','-gravity SouthEast']
    }
    
    __max_width = 100
    
    __max_height = 100
    
    __padding = 10
    
    __transparency = "50%"
    
    #源文件
    __source_file = ''
    #临时文件
    __tmp_file = ''
    #文件名
    __bname = ''
    #使用驱动
    __drive = {}
    #宽高结果
    __wh_result = {}
    #文件副本
    __copy_file = ''
    #目标路径
    __target_page = ''
    #size
    __scale_size = {}
    #目标文件名
    __target_file_name = ''
    #是否加logo
    __add_logo = False
    
    __make_way = ''
    
    def __init__(self, source, targetPage, size={'w':20,'h':20}, drive="gm", tmpPage='/tmp', addLogo=False):
        ''' 初始化 '''
        if drive == 'gm':
            self.__drive = self.__gm_script
        else:
            self.__drive = self.__im_script
        self.__source_file = source.replace("\\",'')
        self.__scale_size = size
        self.__target_page = targetPage.replace("\\",'')
        tmpName = time.time()
        self.__add_logo = addLogo
        #临时文件
        self.__bname       = os.path.basename(source)
        splitext           = os.path.splitext(self.__bname)
        self.__copy_file   = '%s/%s_copy_%s%s' % ( tmpPage ,splitext[0] ,tmpName ,splitext[1] )
        self.__tmp_file    = "%s/%s_tmp_%s%s" % ( tmpPage ,splitext[0] ,tmpName ,splitext[1] )
        
        #目标文件名
        fileName ,suffix= os.path.splitext(self.__bname)
        target_file_name = "%s%s%s" % ( fileName ,self.makeTargetName() ,suffix )
        self.__target_file_name = os.path.normpath( os.path.join( self.__target_page , target_file_name) )
        #self.run()
    
    def setWay(self, way=''):
        ''' 缩略方式 '''
        self.__make_way = way
    
    def setLogo(self, logo_script=None):
        ''' 设置logo '''
        if logo_script != None:
            self.__logo_script = logo_script
    
    def run(self):
        ''' 运行 '''
	if os.path.exists(self.__target_file_name) == True or os.path.exists(self.__source_file) == False:
            return False
        ''' 创建副本 '''
        script = self.__drive['transcript'] % ( self.__source_file, self.__copy_file)
        status , result = xConvert.cmd(script)
        if status != 0:
            xLogging.handler( ' image not exists %s' % script )
            return False
        #获取图片信息
        script = self.__drive['image_info'] % ( self.__copy_file)
        status , result = xConvert.cmd(script)
        if status != 0:
            xLogging.handler( ' get img info error %s' % script )
            return False
        info = result.split(' ',3)
        #CMYK处理
        if info[2].strip().replace(",",'') == "ColorSeparation" or info[2].strip() == "DirectClassCMYK":
            script = self.__drive['colorspace'] % ( self.__copy_file )
            xConvert.cmd(script)
        ''' 获取图片宽高 '''
        #重置宽高
        result = result.strip()
        self.__wh_result = self.rebuildSize (result)
        if_img = len( result.split(',') ) > 2
        
        #建立文件夹
        if os.path.exists( self.__target_page ) == False:
            try:
                os.makedirs(self.__target_page)
            except:
                xLogging.handler( ' make dir %s' % os.error )
                return False
        
        #判断是否为gif
        if if_img == True:
            make_ok = self.makeGif()
            if make_ok == True:
                self.makeImg( self.__tmp_file, self.__target_file_name )
        else:
            self.makeImg( self.__copy_file, self.__tmp_file )
            if self.__add_logo == True:
                #水印方法1
                self.waterMark(self.__tmp_file ,self.__target_file_name)
                #水印方法2
                #self.waterMarkComposite(self.__tmp_file ,self.__target_file_name)
            else:
                script = self.__drive['export'] % (self.__tmp_file, self.__target_file_name)
                status, output = xConvert.cmd(script)
                if status != 0:
                    xLogging.handler( ' export error %s' % script )
        #删除临时文件
        self.unlink (self.__tmp_file)
        self.unlink (self.__copy_file)
	return True
    
    def unlink(self,file):
        if os.path.exists(file):
            os.unlink(file)
    
    def makeTargetName(self, lenNum = 3):
        ''' 合成文件标识 '''
        w = '%s' % self.__scale_size['w']
        h = '%s' % self.__scale_size['h']
        fileW = ''.join(w)
        for rw in range( lenNum - len( w ) ):
            fileW = '%s%s' % ('0',fileW)
        fileH = ''.join(h)
        for rh in range( lenNum - len( h ) ):
            fileH = '%s%s' % ('0',fileH)
        return '%s%s' % (fileW,fileH)
    
    def imgWH(self,images):
        script = self.__drive['identify2'] % images
        logo_status , logo_result = xConvert.cmd(script)
        result = {}
        result['w'] , result['h'] = logo_result.strip().split(' ')
        return result
    
    def waterMarkComposite(self, source, target):
        if self.__scale_size['w'] > self.__max_width and self.__scale_size['h'] > self.__max_height:
            gravity = self.__logo_script['logo'][1]
            logo = self.__logo_script['logo'][0]
            script = self.__drive['waterMark2'] % (self.__transparency, gravity, logo, source,target)
        else:
            script = self.__drive['export'] % (source, target)
        status, output = xConvert.cmd(script)
        if status != 0:
            xLogging.handler( ' water mark logo %s' % script )
            return False
    
    def waterMark(self, source, target):
        ''' 加水印 '''
        script = self.__drive['identify2'] % source
        status , result = xConvert.cmd(script)
        if status != 0:
            xLogging.handler( ' water mark file error %s' % script )
            return False
        w , h = result.split(' ')
        #判断是否需要加水印
        if self.__scale_size['w'] > self.__max_width and self.__scale_size['h'] > self.__max_height:
            sourceWH1 = '%sx%s' % (w,h)
            sourceWH2 = '%s,%s' % (w,h)
            drawScript = ''
            for k in self.__logo_script:
                lWH = self.imgWH(self.__logo_script[k][0])
                x0 = int(w) - ( self.__padding + int(lWH['w']) )
                y0 = int(h) - ( self.__padding + int(lWH['h']) )
                x1 = 0
                y1 = 0
                drawScript += self.__drive['draw'] % ( x0,y0,x1,y1, self.__logo_script[k][0], self.__logo_script[k][1])
            script = self.__drive['watermark'] % (sourceWH1, sourceWH2, source, drawScript ,target)
        else:
            script = self.__drive['export'] % (source, target)
        status, output = xConvert.cmd(script)
        if status != 0:
            xLogging.handler( ' water mark logo %s' % script )
            return False

    def makeImg(self ,source ,target):
        ''' 判断是等比缩放还是缩放 '''
        size = self.__scale_size
        if self.__make_way == 'scale':
            self.scale(source, target, size)
        elif self.__make_way == 'resize':
            self.resize(source, target, size)
        else:
            if int(size['w']) > int(self.__max_width):
                self.scale(source, target, size)
            else:
                self.resize(source, target, size)
    
    def resize(self ,source, target, size):
        scale = self.scaleCount(self.__wh_result,size)
        script = self.__drive['resize'] % (source, int(scale['max_scale']) ,int(scale['max_scale']) ,5 ,5 ,int(scale['w']),int(scale['h']) , target)
        xConvert.cmd(script)
    
    '''
        缩放比例
    '''
    def scaleCount(self, out_wh, scale_wh):
        scale_result = {}
        oldScale = float( int(out_wh['w']) / int(out_wh['h']) )
        configScale = float( int(scale_wh['w']) / int(scale_wh['h']) )
        if oldScale == 0.0:
            oldScale = 1
        if configScale == 0.0:
            configScale = 1
        if out_wh['w'] < out_wh['h']:
            scale_result['max_scale'] = out_wh['w']
        else:
            scale_result['max_scale'] = out_wh['h']
        if oldScale >= configScale:
            scale_result['w'] = int(int(scale_wh['h']) * configScale )
            scale_result['h'] = scale_wh['h']
        else:
            scale_result['w'] = int(out_wh['w'])
            scale_result['h'] = int( int(out_wh['w']) / oldScale )
        scale_result['x'] = math.floor( (int(scale_result['max_scale']) - int(scale_result['w'])) / 2 )
        scale_result['y'] = math.floor( (int(scale_result['max_scale']) - int(scale_result['h'])) / 2 )
        return scale_result
    
    def scale(self, source, target, size):
        w,h = size['w'],size['h']
        if int(size['w']) >= int(self.__wh_result['w']):
            w = int(self.__wh_result['w'])
        if int(size['h']) >= int(self.__wh_result['h']):
            h = int(self.__wh_result['h'])
        script = self.__drive['scale'] % (source, w, h, target)
        xConvert.cmd(script)

    def makeGif(self):
        script = self.__drive['coalesce'] % (self.__copy_file,self.__tmp_file)
        status , result = xConvert.cmd(script)
        if status != 0:
            xLogging.handler( ' make gif error %s ' % script )
            return False
        return True
    
    def rebuildSize(self, str ):
        ''' 重新设置宽高 '''
        width, height = 0 ,0
        for wh in str.split(","):
            if wh == "":
                continue
            w ,h ,r = wh.strip().split(" ")
            intw = int(w)
            inth = int(h)
            if intw > width:
                width = intw
            if inth > height:
                height = inth
        return {'w':width,'h':height}
    
    @staticmethod
    def cmd(cmd):
        ''' 执行脚本 '''
        #xLogging.handler( cmd ,tag='cmd script',types='cmd')
        print cmd
        return commands.getstatusoutput(cmd)
if __name__ == "__main__":
    x = xConvert('./img/1.png','./img/thumb/2009/11/10/15',{'w':300,'h':300},'gm')
    #x = xConvert('/Data/script/gm/cmyk.jpg','/Data/upload/st001/shimao/thumb/2009/11/10/15',{'w':300,'h':300},'im')
    x.run()
