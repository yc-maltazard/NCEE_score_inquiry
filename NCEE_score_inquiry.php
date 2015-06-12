<?php
    header( "Content-type: text/html; charset=utf8" );
    error_reporting( E_ALL & ~ E_NOTICE ) ;
 
    require_once "Snoopy.class.php";
    require_once "simple_html_dom.php";
 
    class response{};
 
    if( ! $_POST )
    {
        echo "@method   POST" . '<br/>' ;
        echo "@param1   KSH     准考证号." . '<br/>' ;
        echo "@param2   BMXH    报名序号." . '<br/>' ;
        echo "@param3   SFZH    身份证号." ;
        exit();
    } else {
        if ( isset( $_POST['KSH'] ) && isset( $_POST['BMXH'] ) )
        {
            $frm['KSH']     = addcslashes( $_POST['KSH'] ) ;
            $frm['BMXH']    = addcslashes( $_POST['BMXH'] ) ;
            if( isset( $_POST['SFZH'] ) )
                $frm['SFZH']    = addcslashes( $_POST['SFZH'] ) ;
        } else {
            echo "准考证号, 报名序号 必填!" ;
        }
    }
 
    $response = new response;
 
    $snoopy = new Snoopy ;
    $snoopy->agent = "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.56 Safari/536.5";
    $snoopy->referer = "http://www.haedu.gov.cn/hadoe_plus/gk_cx/query.aspx" ;
    $action = "http://www.heao.gov.cn/PZQuery/PZCJQuery.aspx";
    $snoopy->submit($action,$frm);
 
    $dom = str_get_html( $snoopy->results ) ;
 
    if( count( $dom->find( 'div.result span#ErrorMSG' ) ) == 1 ){
 
        $response->status    =   'failed' ;
        $response->msg       =   $dom->find( 'div.result span#ErrorMSG', 0 )->text() ;
        echo json_encode( $response ) ;
 
    }else{
 
        $tbl = $dom->find( 'div.result p.queryresult table', 0 ) ;
        $response->status    =   'success' ;
        $data = array(
                'id_card'   => $tbl->find( "td.common", 2)->text() ,
                'name'      => $tbl->find( "td.common", 3)->text() ,
                'yuwen'     => $tbl->find( "td.common", 4)->text() ,
                'shuxue'    => $tbl->find( "td.common", 5)->text() ,
                'yingyu'    => $tbl->find( "td.common", 6)->text() ,
                'zonghe'    => $tbl->find( "td.common", 7)->text() ,
                'zongfen'   => $tbl->find( "td.common", 8)->text() ,
                'tingli'    => $tbl->find( "td.common", 9)->text() ,
            );
 
        $response->data = $data ; 
        echo json_encode( $response ) ;
    }