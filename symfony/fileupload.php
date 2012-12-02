<h3>FileBag</h3>
处理了多文件和数组文件的存储;依据上传文件的name值,将其信息使用UploadedFile.php实例化后,保存到$parameters参数中.



<form action="index.php" method="post" enctype="multipart/form-data" >
	<input type="file" name="name" id="">
	<input type="submit" value="">
</form>


保存一个文件

$request = Request::createFromGlobals();

$uploadedFile = $request->files->get('name');

$uploadedFile->move('./',$uploadedFile->getClientOriginalName());



表单中enctype="multipart/form-data"的意思，是设置表单的MIME编码。默认情况，这个编码格式是application/x-www-form-urlencoded，不能用于文件上传；只有使用了multipart/form-data，才能完整的传递文件数据，进行下面的操作.enctype="multipart/form-data"是上传二进制数据; 

注意这一行：
Content-Type: multipart/form-data; boundary=---7d33a816d302b6
根据 rfc1867, multipart/form-data是必须的.
---7d33a816d302b6 是分隔符，分隔多个文件、表单项。其中33a816d302b6 是即时生成的一个数字，用以确保整个分隔符不会在文件或表单项的内容中出现。前面的 ---7d 是 IE 特有的标志。 Mozila 为---71

文件test.php
<pre lang='php'>
	<form action="index.php" method="post" enctype="multipart/form-data" >
	<input type="file" name="name" id="">
	<input type="file" name="files[]" id="">
	<input type="file" name="files[]" id="">

	<input type="submit" value="">
</form>

</pre>

文件index.php

<pre lang='php'>

print_r($_FILES);

<!-- Array
(
    [name] => Array
        (
            [name] => AddEmotion.htm
            [type] => text/html
            [tmp_name] => E:\Server\xampp\tmp\php9FDE.tmp
            [error] => 0
            [size] => 893
        )
    [files] => Array
        (
            [name] => Array
                (
                    [0] => AddEmotion.htm
                    [1] => AddEmotion.htm
                )

            [type] => Array
                (
                    [0] => text/html
                    [1] => text/html
                )

            [tmp_name] => Array
                (
                    [0] => E:\Server\xampp\tmp\php9FE0.tmp
                    [1] => E:\Server\xampp\tmp\php9FE1.tmp
                )

            [error] => Array
                (
                    [0] => 0
                    [1] => 0
                )

            [size] => Array
                (
                    [0] => 893
                    [1] => 893
                )

        ) -->
</pre>



file.php 主要实现了获取文件的扩展名和移动文件


ExtensionGuesser  根据mimi类型,去猜测文件扩展名

MimeTypeGuesser   根据文件名,猜测mini类型


