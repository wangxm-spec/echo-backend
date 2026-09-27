InfoDlg.clearData = function () {
    this.formData = {};
};

InfoDlg.set = function (key, value) {
    this.formData[key] = (typeof value == "undefined") ? $("#" + key).val() : value;
    return this;
};

InfoDlg.setArray = function (key, value) {
    var images = [];
    $("input[name='" + key + "[]']").each(function() {
        var val = $(this).val();
        if (val && val.trim() !== '') {
            images.push(val.trim());
        }
    });
    this.formData[key] = images;
};

InfoDlg.get = function (key) {
    return $("#" + key).val();
};

InfoDlg.close = function () {
    parent.layer.close(parent.layer.getFrameIndex(window.name));
};

/**
 * 单图上传封装
 * @param {string} inputId - 要回填的input框ID
 * @param {string} title - 弹窗标题，默认"上传图片"
 * @param {string} url - 上传接口地址，默认 admin/upload/image
 */
InfoDlg.uploadImage = function (inputId, title, url) {
    title = title || '上传图片';
    url = url || "/admin/upload/image";
    localStorage.setItem('upload_input_id', inputId);
    layer.open({
        type: 2,
        title: title,
        area: ['600px', '500px'],
        fix: true,
        content: url,
        end: function () {
            var inId = localStorage.getItem('upload_input_id');
            var src = localStorage.getItem('uploaded_image');
            if (src && inId) {
                $('#' + inId).val(src);
                localStorage.removeItem('uploaded_image');
                localStorage.removeItem('upload_input_id');
            }
        }
    });
};

/**
 * 多图上传封装（用于商品图片集等场景）
 * @param {string} containerId - 预览容器的ID
 * @param {string} inputName - 隐藏input的name属性
 * @param {number} maxCount - 最大图片数量，默认9
 * @param {string} url - 上传接口地址，默认 admin/upload/multiImage
 */
InfoDlg.uploadMultiImages = function (containerId, inputName, maxCount, url) {
    maxCount = maxCount || 9;
    url = url || "/admin/upload/multiImage";
    var container = $('#' + containerId);
    var currentCount = container.find('.img-item').length;
    if (currentCount >= maxCount) {
        com.error('最多只能上传' + maxCount + '张图片');
        return;
    }
    var remain = maxCount - currentCount;
    layer.open({
        type: 2,
        title: '批量上传图片（剩余可上传' + remain + '张）',
        area: ['700px', '500px'],
        fix: true,
        content: url + '?max=' + remain,
        end: function () {
            var images = JSON.parse(localStorage.getItem('uploaded_multi_images') || '[]');
            if (images && images.length > 0) {
                for (var i = 0; i < images.length; i++) {
                    var html = '<div class="img-item" style="display:inline-block;position:relative;margin:5px;">' +
                        '<img src="' + images[i] + '" style="width:80px;height:80px;border:1px solid #ddd;">' +
                        '<span onclick="InfoDlg.removeImage(this)" style="position:absolute;top:-8px;right:-8px;width:20px;height:20px;background:#f00;color:#fff;border-radius:50%;text-align:center;line-height:20px;cursor:pointer;font-size:12px;">×</span>' +
                        '<input type="hidden" name="' + inputName + '[]" value="' + images[i] + '">' +
                        '</div>';
                    container.append(html);
                }
                localStorage.removeItem('uploaded_multi_images');
            }
        }
    });
};

/**
 * 移除已上传的图片
 * @param {HTMLElement} obj - 删除按钮元素
 */
InfoDlg.removeImage = function (obj) {
    $(obj).parent('.img-item').remove();
};

/**
 * 收集多图数据到隐藏字段（提交表单前调用）
 * @param {string} containerId - 预览容器ID
 * @param {string} inputName - 隐藏input的name属性
 * @param {string} targetId - 要插入隐藏字段的表单ID
 */
InfoDlg.collectMultiImages = function (containerId, inputName, targetId) {
    var images = [];
    $('#' + containerId + ' input[name="' + inputName + '[]"]').each(function () {
        images.push($(this).val());
    });
    $('input[name="' + inputName + '"]').remove();
    $('<input type="hidden" name="' + inputName + '" value="' + images.join(',') + '">').appendTo('#' + targetId);
};
