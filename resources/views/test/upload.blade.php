

<form method="post" action="/api/upload" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input name="file" type="file" />
    <input type="submit" value="上传"/>
</form>


