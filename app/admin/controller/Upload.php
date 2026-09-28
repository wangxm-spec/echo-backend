<?php

namespace app\admin\controller;

class Upload extends Base
{
    public function image()
    {
        if (!$this->request->isPost()) {
            return view('public/upload/image');
        }
        $file = $this->request->file('file');
        if (!$file) {
            return $this->error('请选择上传文件');
        }
        if (!$file->isValid()) {
            return $this->error('文件上传失败，请重试');
        }
        if ($file->getSize() > 5 * 1024 * 1024) {
            return $this->error('文件不能超过 5MB');
        }
        $allowExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        $ext = strtolower($file->getUploadExtension() ?: '');
        if (!in_array($ext, $allowExt, true)) {
            return $this->error('不支持的文件类型');
        }
        $dateDir  = date('Ymd');
        $filename = md5(uniqid((string)mt_rand(), true)) . '.' . $ext;
        $saveDir  = public_path() . '/storage/images/' . $dateDir;
        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0755, true);
        }
        $file->move($saveDir . '/' . $filename);
        $url = '/storage/images/' . $dateDir . '/' . $filename;
        return $this->success('上传成功', ['url' => $url]);
    }
}