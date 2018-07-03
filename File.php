<?php
namespace app\admin\controller;
use think\Controller;
use think\Loader;
use think\Request;
use think\Response;
use think\Url;
use think\Config;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpSpreadsheet\IOFactory as IO;

use SplFileObject;
/**
 * 文件处理控制
 */
class File extends Controller
{ 
    /**
     * 常用扩展名与content-type对应关系
     * @var array
     */
    protected $extToTpye = [
                'doc' => 'application/msword','gif' => 'image/gif','html' => 'text/html','img' => 'application/x-img',
                'jpe' => 'image/jpeg','jpeg' => 'image/jpeg','jpg' => 'application/x-jpg','png' => 'application/x-png',
                'wav' => 'audio/wav','mp3' => 'audio/mp3','xls' => 'application/vnd.ms-excel','xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document','pdf' => 'application/pdf'
            ];

    protected $extToMethod = [
                'doc' => 'wordToHtml','gif' => 'readFile','html' => 'readFile','img' => 'readFile',
                'jpe' => 'readFile','jpeg' => 'readFile','jpg' => 'readFile','png' => 'readFile',
                'wav' => 'readFile','mp3' => 'readFile','xls' => 'excelToHtml','xlsx' => 'excelToHtml',
                'docx' => 'wordToHtml','pdf' => 'readFile'
            ];

    /**
     * 展示文件
     * @Author   liulong                  <335111164@qq.com>
     * @DateTime 2018-06-28T18:35:19+0800
     * @return   [type]                   [description]
     */
    public function index()
    {
        Config::set('url_common_param',true);
        $data = Request::instance()->request();
        if(!empty($data['path']))
            $file = $data['path'].DS.$data['filename'];
        else
            $file = $data['filename'];
        $Object = new SplFileObject($file,'r');
        //文件扩展名
        $filext = $Object->getExtension();
        $this->assign('filext',$filext);
        $this->assign('readfile',Url::build("admin/File/{$this->extToMethod[$filext]}?path={$data['path']}&filename={$data['filename']}"));
        return $this->fetch();
    }

    /**
     * 读取文件
     * @Author   liulong                  <335111164@qq.com>
     * @DateTime 2018-06-28T15:50:13+0800
     * @return   [type]                   [description]
     */
    public function readFile()
    {
        //接受参数 文件目录path 文件名filename 自定义文件名 setname
        $data = Request::instance()->request();
        if(empty($data['filename'])){
            $this->error('文件名为空');
        }
        if(!empty($data['path']))
            $file = $data['path'].DS.$data['filename'];
        else
            $file = $data['filename'];
        //判断文件是否存在
        if(!file_exists($file)){
            $this->error('文件不存在');
        }
        $Object = new SplFileObject($file,'r');
        //文件扩展名
        $filext = $Object->getExtension();
        if(!isset($this->extToTpye[$filext])){
            $this->error('未设置content-type');
        }
        if(!empty($data['setname'])){
            $setname = $data['setname'];
        }else{
            $setname = $data['filename'];
        }
        //文件内容
        $file_content = $Object->fread($Object->getSize());
        return Response::create($file_content)->contentType($this->extToTpye[$filext])
                ->header('Content-disposition', 'attachment; filename='.$setname)
                ->header('Accept-Length', $Object->getSize())
                ->header('Accept-Ranges', 'bytes');
    }

    /**
     * word转HTML
     * @Author   liulong                  <335111164@qq.com>
     * @DateTime 2018-07-02T10:42:38+0800
     * @return   [type]                   [description]
     */
    public function wordToHtml()
    {
        $data = Request::instance()->request();
        if(empty($data['filename'])){
            $this->error('文件名为空');
        }
        if(!empty($data['path']))
            $file = $data['path'].DS.$data['filename'];
        else
            $file = $data['filename'];
        //判断文件是否存在
        if(!file_exists($file)){
            $this->error('文件不存在');
        }
        $html_dir = '../preview';
        $phpWord = IOFactory::load($file);
        $xmlWriter = IOFactory::createWriter($phpWord, "HTML");
        $view = $html_dir.DS.'word_'.explode('.', $data['filename'])[0].'.html';
        $xmlWriter->save($view);
        return $this->fetch($view); 
    }

    /**
     * excel转HTML
     * @Author   liulong                  <335111164@qq.com>
     * @DateTime 2018-07-03T15:37:50+0800
     * @return   [type]                   [description]
     */
    public function excelToHtml()
    {
        $data = Request::instance()->request();
        if(empty($data['filename'])){
            $this->error('文件名为空');
        }
        if(!empty($data['path']))
            $file = $data['path'].DS.$data['filename'];
        else
            $file = $data['filename'];
        //判断文件是否存在
        if(!file_exists($file)){
            $this->error('文件不存在');
        }
        $Object = new SplFileObject($file,'r');
        //文件扩展名
        $filext = $Object->getExtension();
        $reader = IO::createReader(ucfirst($filext));
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        
        $writer = IO::createWriter($spreadsheet, 'Html');
        $writer->setIncludeCharts(true);
        $html_dir = '../preview';
        $view = $html_dir.DS.'excel_'.explode('.', $data['filename'])[0].'.html';
        $writer->save($view);
        return $this->fetch($view);
    }

}
