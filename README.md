# lmyview
File Preview

web文件预览 支持picture,word,pdf,excel,audio,video

调用方式（基于jquery）
var config = {
    elem:'#content',
    url:'{$readfile}',
    ext:'{$filext}',
};
$.lmyview(config);

说明：word，excel文件的预览需要后端转码，同时提供基于tp5的PHP代码（容易修改为原生PHP）
