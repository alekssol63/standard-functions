<?php
mb_internal_encoding('utf-8');
function getname($text)
 {
   $mask_str=",.!?:;(-)][\"'/\\";

   $signs = array('-',',',':',';','[',']','!','?','"','\'', );

   $outstr='';
   $tmp_str=$text;

   $words_count=0;//счетчик слов

    while ($text)
    {
      if ($words_count==5)
      {
        $outstr=$outstr."...";
        break;
      }
      else
      {
        if (mb_substr($text,0,mb_strpos($text," "))) {
        $tmp_str=mb_substr($text,0,mb_strpos($text," "));
        }
        else {$tmp_str=$text;}

          $text=mb_substr($text,mb_strlen($tmp_str)+1,mb_strlen($text));//вырезаем найденное слово с пробелом после него
          if (strpbrk($tmp_str, $mask_str))
          {//проверяем начие символов маски в фразе
            $symb_str=(strpbrk($tmp_str, $mask_str));//строка

            if ((mb_strlen($symb_str)==1) && (mb_strpos($tmp_str,$symb_str) == mb_strlen($tmp_str)-1))//знак в конце фразы
            {

              $end_symb=mb_substr($tmp_str,mb_strlen($tmp_str)-1,mb_strlen($tmp_str));//выделяем последний символ
              if($end_symb=='.')
              {
                $tmp_str=mb_substr($tmp_str,0,mb_strlen($tmp_str)-1);
                $outstr=$outstr." ".$tmp_str;
                break;
              }
              elseif (in_array($end_symb,$signs))
              {

                $tmp_str=mb_substr($tmp_str,0,mb_strlen($tmp_str)-1);

                $outstr=$outstr." ".$tmp_str."...";

                break;
              }

          }
          elseif (mb_strpos($tmp_str,$symb_str)==0)//знак в начале фразы
          {

            $symb_str_start=strspn($tmp_str,$mask_str);
            $tmp_str=mb_substr($tmp_str,$symb_str_start,mb_strlen($tmp_str)-$symb_str_start);
            if   ($outstr!='')break;
            elseif (strpbrk($tmp_str, $mask_str))
            {//проверяем начие символов маски в фразе
              $symb_str=(strpbrk($tmp_str, $mask_str));//строка
              $tmp_str=mb_substr($tmp_str,0,mb_strpos($tmp_str,$symb_str));

              $outstr=$outstr." ".$tmp_str."...";
              break;
            }
          }
          else
          {//знаки в середине фразы

            $tmp_str=mb_substr($tmp_str,0,mb_strpos($tmp_str,$symb_str));
            $outstr=$outstr." ".$tmp_str."...";
            break;
          }
       }
else{
  $outstr=$outstr." ".$tmp_str;};

  $words_count++;
}
}
  return $outstr;
}

//работа с полученными данными
$str=strip_tags(file_get_contents(__DIR__.'/vkapi.txt'));
$str = substr_replace($str,'',0,strpos($str,"["));
$str=rtrim($str);

$str=substr_replace($str,'',strlen($str)-1,1);
$data_arr=json_decode($str);
?>

<html>
<?php

foreach ($data_arr as $key => $value)
{
  if (isset($data_arr[$key]->attachment->photo->src))
  {
    $url=$data_arr[$key]->attachment->photo->src;//ссылка на фотографию isset???
    $text=$data_arr[$key]->text;//текст записи
    $img_arr[]=array("likes"=>$data_arr[$key]->likes->count, "pic_name"=>getname($text),"url"=>$url);

}

}
//сортировка по количеству лайков
foreach ($img_arr  as $key => $val) $lks[$key]=$val['likes'];
array_multisort($lks, SORT_DESC, $img_arr);

foreach ($img_arr as $key => $value)
 {
  echo($img_arr[$key]["pic_name"]);
  echo "</br>";
  echo"<img "."src=".$img_arr[$key]["url"]." ></br>";
  echo "ЛАЙКОВ: ".$img_arr[$key]["likes"];
  echo "</br>";

 }
?>
</html>
