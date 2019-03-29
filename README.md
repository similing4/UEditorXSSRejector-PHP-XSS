# UEditorXSSRejector-PHP-XSS
通过PHP白名单法清理来自前端的富文本以防止XSS攻击
# 原理
## 1.本类使用了白名单法，使用了simple_html_dom。
## 2.将传入数据中的特殊情况进行过滤，例如
```html
<<SCRIPT>alert("XSS");//<</SCRIPT>
```
```html
&#x000;
```
以上两种代码，第一种会被simple_html_dom误将<script>认为文本，以造成问题。解决方案是在<<中添加空格。第二种会被浏览器解析成二进制字符，解决方案是直接将&#替换掉。

## 3.将传入的参数解析为dom元素进行树递归遍历
## 4.列出白名单以排除非法元素
因为有可能在text（被simple_html_dom解析为文本）部分存在<与>号，因此要对这些进行encode编码。
```php
$domitem->innertext=htmlspecialchars($domitem->innertext);
```
还有对于javascript:、ie的expression需要单独进行判断。
```
if(!in_array($key, $this->allowParams[$domitem->tag])||strpos($d, 'script')!==false||strpos($d, '&#x')!==false||($domitem->tag=='img'&&$key=="style"&&strpos($d, 'expression')!==false))
						$domitem->removeAttribute($key);
```
# 使用方法
使用test.php，require文件UEditorXSSRejector.php后即可调用。
test.php文件中的方法在TP5中请加入对应应用的common.php中，TP3请加入对应的function.php中并use Org\Util;。
UEditorXSSRejector.php与simple_html_dom.php文件，TP5请放到extend/org/Util文件夹下。TP3可放入对应的org/Util文件夹下。
方法remove_xss传入的是tp处理过的参数经过htmlspecialchars_decode后的数据。如果是tp没有处理的原生数据可以直接传入。返回值为处理后的数据。
例：
```php
function makeRe($content){
  $content = htmlspecialchars_decode($content);
  $content = remove_xss($content);
  M("Re")->add(["content"=>$content]);
  $this->success("成功");
}
```
