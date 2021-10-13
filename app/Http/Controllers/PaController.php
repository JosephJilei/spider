<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use simple_html_dom;
use Excel;
use Storage;
use App\Exports\ProductExport;
//use QL\QueryList;

class PaController extends Controller
{
    //made in china
    public function index(Request $request)
    {
        $limit = 48;
        set_time_limit(0);
        $name = '产品目录手册';
        $group = $request->input('group');
        $item = $request->input('item');

        if(!$item || !$group){
            dd('没有参数');
        }

        $prefix = [
            'Metal Parts'=>'metal',
            'Stamping Parts'=>'stamp'
        ];

        $titleSuffix = [
                'Metal Parts'=>'CNC Turning Stamping Casting Metal Parts',
                'Stamping Parts'=>'Casting CNC Turning Stamping Parts',
            ];

        $html = new simple_html_dom();
        @$html->load_file(storage_path().'/app/public/html/'.$group.'/48-'.$item.'.html');
        $list = $html->find('div.prod-title a');
        //分析html
        //编号 title url 生成excel
        $data = [['分组', '型号', '标题', '产品链接']];
        $hrefArr = [];
        foreach($list as $key=>$l){
            $no = $key + 1 + ($item-1)*$limit;
            $noStr = 'oem-cm-'.$prefix[$group].$this->renameFolder($no);
            $title = $l->attr['title'];
            $href = $l->attr['href'];

            //是否有关键词
            $title = preg_replace('/(C|c)ast((ings|ing)?)\s?/', '', $title);
            $title = preg_replace('/(T|t)urn(ings|ing)?\s?/', '', $title);
            $title = preg_replace('/(S|s)tamp(ings|ing)?\s?/', '', $title);
            $title = preg_replace('/(P|p)art(s)?\s?/', '', $title);
            $title = preg_replace('/(CNC|cnc)\s?/', '', $title);
            $title = preg_replace('/(M|m)etal\s?/', '', $title);
            $title = preg_replace('/(A|a)libaba\s?/', '', $title);
            $title = preg_replace('/for\s?/', '', $title);
            $title = preg_replace('/(,|-|\/|.|;)/', '', $title);
            
            $title = trim($title);
            $title = ucwords($title);

            //去掉重复词
            $titelArr = explode(' ', $title);
            $titleArrNew = array_unique($titelArr);
            $title = implode(' ', $titleArrNew);
            $titleStr = $title.' '.$titleSuffix[$group];

            array_push($data, [$group, $noStr, $titleStr, $href]);
            $hrefArr[$no] = $href;
        }

        //下载图片
        // foreach($hrefArr as $key => $href){
        //     //$productHtml = $this->httpCurl($href);
        //     @$html->load_file($href);
        //     $folderName = $group.'/img/'.$this->renameFolder($key);
        //     Storage::disk('public')->makeDirectory($folderName);

        //     $imgList = $html->find('div.sr-proMainInfo-slide-picItem');
        //     //video src
        //     //img fsrc
        //     foreach($imgList as $img){
        //         if(isset($img->attr['fsrc'])){
        //             $filename = ($key+1).'-'.md5(microtime(true).mt_rand(1,9999)).'.jpg';
        //             Storage::disk('public')->put($folderName.$filename, file_get_contents('https:'.$img->attr['fsrc']));
        //         }
        //     }
        // }

        //写入excel
        $this->export($data, $name, $group, $item);
    }

    public function plastic(Request $request)
    {
        $limit = 48;
        set_time_limit(0);
        $name = '产品目录手册';
        //$url = $request->input('url');
        $group = $request->input('group');
        $item = $request->input('item');

        if(!$item || $group){
            dd('没有参数');
        }

        $noArr = [
                    'Electrical Plastic Parts'=>'electricalplastic',
                    '',
                    '',
                    ''
                ];

        //抓取html
        //$url = 'https://szytjd.en.made-in-china.com/productList?username=&pageNumber=2&pageSize=48&viewType=1&isByGroup=1&pageUrlFrom=1&productGroupOrCatId=YqkmyMEPJRUe&searchKeyword=&searchKeywordSide=&searchKeywordList=&selectedFeaturedType=&selectedSpotlightId=&viewPageSize=48';
        //$parseHtml = $this->httpCurl($url);
        //dd($parseHtml);
        $html = new simple_html_dom();
        //@$html->load_file($url);
        @$html->load_file(storage_path().'/app/public/html/'.$group.'/48-'.$item.'.html');
        $list = $html->find('div.prod-title a');
        //dd($list);
        //分析html
        //编号 title url 生成excel
        $data = [['分组', '型号', '标题', '产品链接']];
        $hrefArr = [];
        foreach($list as $key=>$l){
            $no = $key + 1 + ($item-1)*$limit;
            $title = $l->attr['title'];
            $href = $l->attr['href'];
            $noStr = 'oem-cm-'.$noArr[$group].renameFolder($no);
            array_push($data, [$group, $no, $title, $href]);
            $hrefArr[$no] = $href;
        }

        //写入excel
        $this->export($data, $name, $group, $item);
        //下载图片 001-1-5
        foreach($hrefArr as $key => $href){
            //$productHtml = $this->httpCurl($href);
            @$html->load_file($href);
            // $priceList = $html->find('strong.red');
            // if(count($priceList) > 1){
            //     $folderName = str_replace('US $', '', $priceList[count($priceList)-1]->innertext).'-'.str_replace('US $', '', $priceList[0]->innertext);
            //     $folderName = $group.'/img/'.$this->renameFolder($key).'-'.$folderName.'/';
            // }else{
            //     $folderName = str_replace('US $', '', $priceList[0]->innertext);
            //     $folderName = str_replace('/ Piece', '', $folderName);
            //     $folderName = $group.'/img/'.$this->renameFolder($key).'-'.$folderName.'/';
            // }

            $folderName = $group.'/img/'.$this->renameFolder($key);
            Storage::disk('public')->makeDirectory($folderName);

            $imgList = $html->find('div.sr-proMainInfo-slide-picItem');
            //video src
            //img fsrc
            foreach($imgList as $key=>$img){
                if(isset($img->attr['fsrc'])){
                    $filename = ($key+1).'-'.md5(microtime(true).mt_rand(1,9999)).'.jpg';
                    Storage::disk('public')->put($folderName.$filename, file_get_contents('https:'.$img->attr['fsrc']));
                }
            }
        }
    }

    //ALIBB viewtype=G
    public function alibaba(Request $request)
    {
        //每页产品数量
        $limit = 48;
        set_time_limit(0);
        $name = '产品目录手册';

        $group = $request->input('group');
        $item = $request->input('item');
        if(!$group || !$item){
            dd('没有参数');
        }

        $prefix = [
                    'Casting Parts'=>'cast',
                    'Stamping Parts'=>'stamp'
                ];

        $titleSuffix = [
                        'Casting Parts'=>'Metal CNC Turning Stamping Casting Parts',
                        'Stamping Parts'=>'Metal Casting CNC Turning Stamping Parts',
                    ];

        $html = new simple_html_dom();
        @$html->load_file(storage_path().'/app/public/html/'.$group.'/48-'.$item.'.html');
        $hrefList = $html->find('div.organic-gallery-offer-section__title a');
        //分析html
        $data = [['分组', '型号', '标题', '产品链接']];
        $hrefArr = [];
        foreach($hrefList as $key=>$l){
            $no = $key + 1 + ($item-1)*$limit;
            $noStr = 'oem-cm-'.$prefix[$group].$this->renameFolder($no);
            $href = 'https:'.$l->attr['href'];
            //$title = $l->attr['title'];
            
            array_push($data, [$group, $noStr, $href]);
            $hrefArr[$no] = $href;
        }

        //下载图片
        foreach($hrefArr as $key => $href){
            @$html->load_file($href);
            $titleList = $html->find('h1.module-pdp-title');
            if($titleList) $title = $titleList[0]->attr['title'];
            else $title = '';

            //是否有关键词
            $title = preg_replace('/(C|c)ast((ings|ing)?)\s?/', '', $title);
            $title = preg_replace('/(T|t)urn(ings|ing)?\s?/', '', $title);
            $title = preg_replace('/(S|s)tamp(ings|ing)?\s?/', '', $title);
            $title = preg_replace('/(P|p)art(s)?\s?/', '', $title);
            $title = preg_replace('/(CNC|cnc)\s?/', '', $title);
            $title = preg_replace('/(M|m)etal\s?/', '', $title);
            $title = preg_replace('/(A|a)libaba\s?/', '', $title);
            $title = preg_replace('/for\s?/', '', $title);
            $title = preg_replace('/(,|-|\/|.|;)/', '', $title);
            $title = trim($title);
            $title = ucwords($title);

            //去掉重复词
            $titelArr = explode(' ', $title);
            $titleArrNew = array_unique($titelArr);
            $title = implode(' ', $titleArrNew);
            $titleStr = $title.' '.$titleSuffix[$group];
            array_splice($data[$key-($item-1)*$limit], 2, 0, [$titleStr]);

            // $folderName = $group.'/img/'.$this->renameFolder($key).'/';
            // Storage::disk('public')->makeDirectory($folderName);
            // $imgList = $html->find('li.main-image-thumb-item > img');
            // //img src
            // foreach($imgList as $key=>$img){
            //     if(isset($img->attr['src'])){
            //         $filename = ($key+1).'-'.md5(microtime(true).mt_rand(1,9999)).'.jpg';
            //         $sourceArr  = explode('_', $img->attr['src']);
            //         array_pop($sourceArr);
            //         Storage::disk('public')->put($folderName.$filename, file_get_contents(implode('_', $sourceArr)));
            //     }
            // }
        }

        //写入excel
        $this->export($data, $name, $group, $item);
    }

    // private function httpCurl($url, $header=null)
    // {
    //     // 1.初始化
    //     $ch = curl_init($url); //请求的地址
    //     // 2.设置选项
    //     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//获取的信息以字符串返回,而不是直接输出(必须)
    //     curl_setopt($ch,CURLOPT_TIMEOUT,30);//超时时间(必须)
    //     curl_setopt($ch, CURLOPT_HEADER,0);// 启用时会将头文件的信息作为数据流输出。
    //     //参数为1表示输出信息头,为0表示不输出
    //     curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false); //不验证证书
    //     curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false); //不验证证书
    //     if(!empty($header)){
    //         curl_setopt($ch,CURLOPT_HTTPHEADER,$header);//设置头信息
    //     }else{
    //         $_head = [
    //             'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0'
    //         ];
    //         curl_setopt($ch,CURLOPT_HTTPHEADER,$_head);
    //     }
    //     // 3.执行
    //     $res = curl_exec($ch);
    //     // 4.关闭
    //     curl_close($ch);
    //     return $res;
    // }

    private function export($data, $name, $group, $item) 
    {
        //Excel::store(new ProductExport($data), $name.'.xlsx', 'public');
        Excel::store(new ProductExport($data), $group.'/'.$name.'-'.$group.'-'.$item.'.xlsx', 'public');
    }

    private function renameFolder($num)
    {
        $length = mb_strlen($num);
        $str = '';
        if($length == 3){
            return $num;
        }else{
            for($i=0;$i<3-$length;$i++){
                $str .= '0';
            }
            return $str.$num;
        }
    }
}
