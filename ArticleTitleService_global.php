<?php
var ArticleTitleService = function (category) {
    var _this = this;
    this.title = "";
    this.category = category;
    $.each(this.components = ["author", "rawTitle", "volume", "option"], function (i, key) {
        _this[key] = "";
    });
};
ArticleTitleService.prototype.update = function (key, value) {
    if (!this.components.hasValue(key))
        return false;
    return this[key] = value == null ? "" : value;
};
ArticleTitleService.prototype.compose = function () {
    return this.title = $.trim([this.composeAuthor(), this.composeTitle(), this.composeVolume(), this.composeOption()].join(" "));
};
ArticleTitleService.prototype.compose_only_volume = function () {
    return this.title = this.composeVolume();
};
ArticleTitleService.prototype.decompose = function (title) {
    var m;
    title = this.convertKanaNumber(title);
    if (m = title.match(/^(\[([^\[\]]+)\] )?(.*?)( (第|全|[0-9]{4}(年|-|\/)?)?[0-9]+(-[0-9])?(,[0-9]+(-[0-9])?)*(巻|集|号)?)?(.*?)$/)) { }
};
ArticleTitleService.prototype.composeAuthor = function () {
    var m, author = $.trim(this.author);
    if (author.match(/^\[[^\[\]]+\]$/)) {
        return author;
    }
    if (author) {
        return "[" + author.replace(/[×,]/g, "x") + "]";
    }
    return "";
};
ArticleTitleService.prototype.composeTitle = function () {
    return $.trim(this.rawTitle);
};
ArticleTitleService.prototype.composeVolume = function () {
    var m, volume;
    if (!(volume = this.convertKanaNumber($.trim(this.volume)))) {
        return "";
    }
    if (["etc", "doujin"].hasValue(this.category)) {
        return volume;
    }
    if (this.category == "magazine" && (m = volume.match(/^([0-9]{4}(年|-|\/))?([0-9\-, ]+)(号)?$/))) {
        var year = m[1] ? m[1].replace("/", "年") : new Date().YYYY() + "年";
        return year + this.formatVolume(m[3]) + (m[4] || "号");
    }
    if (m = volume.match(/^(第|全)?([0-9\-, ]+)(巻|集)?$/)) {
        return (m[1] || "第") + this.formatVolume(m[2]) + (m[3] || "巻");
    }
    return volume;
};
ArticleTitleService.prototype.composeOption = function () {
    return $.trim(this.option);
};
ArticleTitleService.prototype.formatVolume = function (volume) {
    volume = volume.replace(/ /g, "");
    var elems = volume.split(/[\-,]+/g);
    var seps = volume.split(/[0-9]+/g).removeEmpty();
    var maxDecimal = 2;
    var ret = "";
    $.each(elems, function (i, elem) {
        maxDecimal = Math.max(elem.length, maxDecimal);
    });
    $.each(elems, function (i, elem) {
        ret += elem.pad(maxDecimal) + (seps[i] ? seps[i][0] : "");
    });
    return ret;
};
ArticleTitleService.prototype.convertKanaNumber = function (number) {
    var sourceTable = ["０", "１", "２", "３", "４", "５", "６", "７", "８", "９", "ー", "─"];
    var replacementTable = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-", "-"];
    $.each(sourceTable, function (i, char) {
        number = number.replace(new RegExp(char, "g"), replacementTable[i]);
    });
    return number;
};
?>
