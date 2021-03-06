<?php

/**
 * 操纵文件类
 *
 * 例子：
 * Util_File::createDir('a/1/2/3'); 测试建立文件夹 建一个a/1/2/3文件夹
 * Util_File::createFile('b/1/2/3'); 测试建立文件 在b/1/2/文件夹下面建一个3文件
 * Util_File::createFile('b/1/2/3.exe'); 测试建立文件 在b/1/2/文件夹下面建一个3.exe文件
 * Util_File::copyDir('b','d/e'); 测试复制文件夹 建立一个d/e文件夹，把b文件夹下的内容复制进去
 * Util_File::copyFile('b/1/2/3.exe','b/b/3.exe'); 测试复制文件 建立一个b/b文件夹，并把b/1/2文件夹中的3.exe文件复制进去
 * Util_File::moveDir('a/','b/c'); 测试移动文件夹 建立一个b/c文件夹,并把a文件夹下的内容移动进去，并删除a文件夹
 * Util_File::moveFile('b/1/2/3.exe','b/d/3.exe'); 测试移动文件 建立一个b/d文件夹，并把b/1/2中的3.exe移动进去
 * Util_File::unlinkFile('b/d/3.exe'); 测试删除文件 删除b/d/3.exe文件
 * Util_File::unlinkDir('d'); 测试删除文件夹 删除d文件夹
 */
class Util_File {

    /**
     * 建立文件夹
     *
     * @param string $aimUrl
     * @return viod
     */
    public function createDir($aimUrl) {
        $aimUrl = str_replace('', '/', $aimUrl);
        $aimDir = '';
        $arr = explode('/', $aimUrl);
        foreach ($arr as $str) {
            $aimDir .= $str . '/';
            if (!file_exists($aimDir)) {
                mkdir($aimDir);
            }
        }
    }

    /**
     * 建立文件
     *
     * @param string $aimUrl
     * @param boolean $overWrite 该参数控制是否覆盖原文件
     * @return boolean
     */
    public function createFile($aimUrl, $overWrite = false) {
        if (file_exists($aimUrl) && $overWrite == false) {
            return false;
        } elseif (file_exists($aimUrl) && $overWrite == true) {
            Util_File::unlinkFile($aimUrl);
        }
        $aimDir = dirname($aimUrl);
        Util_File::createDir($aimDir);
        touch($aimUrl);
        return true;
    }

    /**
     * 移动文件夹
     *
     * @param string $oldDir
     * @param string $aimDir
     * @param boolean $overWrite 该参数控制是否覆盖原文件
     * @return boolean
     */
    public function moveDir($oldDir, $aimDir, $overWrite = false) {
        $aimDir = str_replace('', '/', $aimDir);
        $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
        $oldDir = str_replace('', '/', $oldDir);
        $oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir . '/';
        if (!is_dir($oldDir)) {
            return false;
        }
        if (!file_exists($aimDir)) {
            Util_File::createDir($aimDir);
        }
        @$dirHandle = opendir($oldDir);
        if (!$dirHandle) {
            return false;
        }
        while (false !== ($file = readdir($dirHandle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (!is_dir($oldDir . $file)) {
                Util_File::moveFile($oldDir . $file, $aimDir . $file, $overWrite);
            } else {
                Util_File::moveDir($oldDir . $file, $aimDir . $file, $overWrite);
            }
        }
        closedir($dirHandle);
        return rmdir($oldDir);
    }

    /**
     * 移动文件
     *
     * @param string $fileUrl
     * @param string $aimUrl
     * @param boolean $overWrite 该参数控制是否覆盖原文件
     * @return boolean
     */
    public function moveFile($fileUrl, $aimUrl, $overWrite = false) {
        if (!file_exists($fileUrl)) {
            return false;
        }
        if (file_exists($aimUrl) && $overWrite = false) {
            return false;
        } elseif (file_exists($aimUrl) && $overWrite = true) {
            Util_File::unlinkFile($aimUrl);
        }
        $aimDir = dirname($aimUrl);
        Util_File::createDir($aimDir);
        rename($fileUrl, $aimUrl);
        return true;
    }

    /**
     * 删除文件夹
     *
     * @param string $aimDir
     * @return boolean
     */
    public function unlinkDir($aimDir) {
        $aimDir = str_replace('', '/', $aimDir);
        $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
        if (!is_dir($aimDir)) {
            return false;
        }
        $dirHandle = opendir($aimDir);
        while (false !== ($file = readdir($dirHandle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (!is_dir($aimDir . $file)) {
                Util_File::unlinkFile($aimDir . $file);
            } else {
                Util_File::unlinkDir($aimDir . $file);
            }
        }
        closedir($dirHandle);
        return rmdir($aimDir);
    }

    /**
     * 删除文件
     *
     * @param string $aimUrl
     * @return boolean
     */
   public  function unlinkFile($aimUrl) {
        if (file_exists($aimUrl)) {
            $nowtime = time();
            $file = filectime($aimUrl);
            if ($nowtime - $file > 7 * 24 * 3600) {
                unlink($aimUrl);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 复制文件夹
     *
     * @param string $oldDir
     * @param string $aimDir
     * @param boolean $overWrite 该参数控制是否覆盖原文件
     * @return boolean
     */
   public function copyDir($oldDir, $aimDir, $overWrite = false) {
        $aimDir = str_replace('', '/', $aimDir);
        $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
        $oldDir = str_replace('', '/', $oldDir);
        $oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir . '/';
        if (!is_dir($oldDir)) {
            return false;
        }
        if (!file_exists($aimDir)) {
            Util_File::createDir($aimDir);
        }
        $dirHandle = opendir($oldDir);
        while (false !== ($file = readdir($dirHandle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (!is_dir($oldDir . $file)) {
                Util_File::copyFile($oldDir . $file, $aimDir . $file, $overWrite);
            } else {
                Util_File::copyDir($oldDir . $file, $aimDir . $file, $overWrite);
            }
        }
        return closedir($dirHandle);
    }

    /**
     * 复制文件
     *
     * @param string $fileUrl
     * @param string $aimUrl
     * @param boolean $overWrite 该参数控制是否覆盖原文件
     * @return boolean
     */
   public function copyFile($fileUrl, $aimUrl, $overWrite = false) {
        if (!file_exists($fileUrl)) {
            return false;
        }
        if (file_exists($aimUrl) && $overWrite == false) {
            return false;
        } elseif (file_exists($aimUrl) && $overWrite == true) {
            Util_File::unlinkFile($aimUrl);
        }
        $aimDir = dirname($aimUrl);
        Util_File::createDir($aimDir);
        copy($fileUrl, $aimUrl);
        return true;
    }

}

?> 