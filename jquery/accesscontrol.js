// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * blocks/taskchain_navigation/accesscontrol.js
 *
 * @package    blocks
 * @subpackage taskchain_navigation
 * @copyright  2014 Gordon Bateson <gordon.bateson@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.0
 */

// set hide all sections when document has loaded
$(document).ready(function(){

    // set wwwroot from page URL
    var wwwroot = location.href.replace(new RegExp("^(.*?)/blocks/taskchain_navigation.*$"), "$1");

    // set URL of the first available help icon
    // this will be used to generate URLs for other images
    var helpiconurl = $("img.iconhelp").first().attr("src");
    if (helpiconurl=='') {
        helpiconurl = wwwroot + "/pix/help.gif";
    }

    // set all itemname cells to uniform width
    var w = 0;
    $("td.itemname").each(function(){
        w = Math.max($(this).width(), w);
    });
    $("td.itemname").each(function(){
        $(this).width(w);
    });

    // get maximum width of rightmost column
    var w = 0;
    $("td.itemselect").each(function(){
        w = Math.max($(this).width(), w);
    });

    // add section toggle images and hide rows
    // (note the maximum width rightmost column)
    $("tr.sectionheading").each(function(){
        $(this).children("th.toggle").each(function(){

            // all rows in this section
            var rows = $(this).closest("tr").nextUntil("tr.sectionheading");

            // check to see if any rows are selected in this section
            var selected = false;
            rows.find("td.itemselect input[type=checkbox]").each(function(){
                if ($(this).prop("checked")) {
                    selected = true;
                }
            });

            // create new IMG element
            var img = document.createElement("IMG");
            if (selected) {
                var src = "t/switch_minus";
            } else {
                var src = "t/switch_plus";
            }
            img.src = helpiconurl.replace('help', src);

            // add IMG click event handler
            $(img).click(function(evt){
                var src = $(this).attr("src");
                if (src.indexOf("minus") >= 0) {
                    $(this).attr("src", src.replace("minus", "plus"));
                    $(this).closest("tr").nextUntil("tr.sectionheading").hide();
                } else {
                    $(this).attr("src", src.replace("plus", "minus"));
                    $(this).closest("tr").nextUntil("tr.sectionheading").show();
                }
            });

            // append IMG
            $(this).append(img);

            // update max width, if necessary
            w = Math.max($(this).width(), w);

            // hide all rows in this section, if none of them are selected
            if (selected==false) {
                rows.hide("fast");
            }
        });
    });

    // standardize width of rightmost column
    $("td.itemselect").each(function(){
        $(this).width(w);
    });
    $("tr.sectionheading th.toggle").each(function(){
        $(this).width(w);
    });

    // add "All / None" toggles to multi-select elements
    $("tr#id_section_activityfilters").nextUntil("tr.sectionheading").find("select[multiple]").each(function(){

        var selector = "#" + $(this).attr("id") + " option";
        $(this).parent("td.itemvalue").prev("td.itemname").each(function(){

            // setup the "All" SPAN
            var txt = document.createTextNode(TCN.msg.all);
            var span1 = $("<span>").append(txt).click(function(evt){
                $(selector).attr("selected", true);
            });

            // setup the "None" SPAN
            var txt = document.createTextNode(TCN.msg.none);
            var span2 = $("<span>").append(txt).click(function(evt){
                $(selector).attr("selected", false);
            });

            // setup the containing DIV ("class" must be a string for IE compatability)
            var txt = document.createTextNode(' / ');
            var div = $("<div>", {"class": "allornone"}).append(span1, txt, span2);

            $(this).append(div);
        });
    });

    // add "All / None" toggle for itemselect checkboxes
    $("table.blockconfigtable td.itemselect").first().each(function(){

        var selector = "table.blockconfigtable td.itemselect input[type=checkbox]";

        // setup the "All" SPAN
        var txt = document.createTextNode(TCN.msg.all);
        var span1 = $("<span>").append(txt).click(function(evt){
            $(selector).attr("checked", true);
        });

        // setup the "None" SPAN
        var txt = document.createTextNode(TCN.msg.none);
        var span2 = $("<span>").append(txt).click(function(evt){
            $(selector).attr("checked", false);
        });

        // setup the containing DIV ("class" must be a string for IE compatability)
        var txt = document.createTextNode(' / ');
        var div = $("<div>", {"class": "allornone"}).append(span1, txt, span2);

        $(this).append(div);
    });

    // setup click handlers on "itemselect" checkboxes
    $("table.blockconfigtable td.itemselect input[type=checkbox][name^=select_]").each(function(){
        $(this).attr("id", "id_" + $(this).attr("name"));
        var id = $(this).attr("id");
        $(this).click(function(evt){
            var textcolor = '';
            var checked = $(this).prop("checked");
            var itemvalues = $(this).parent("td.itemselect").prev("td.itemvalue").find("input,select");
            itemvalues.each(function(){
                if (checked) {
                    this.disabled = (this.disabledvalue ? true : false);
                } else {
                    this.disabledvalue = (this.disabled ? true : false);
                    this.disabled = true;
                }
                if (itemvalues.length==1 && this.type=="checkbox") {
                    this.checked = checked;
                }
                if (textcolor=='') {
                    textcolor = (checked ? 'inherit' : '#999999');
                    $(this).parent("td").css("color", textcolor);
                }
            });
        });
        $(this).triggerHandler("click");
    });

    // setup click handlers to confirm action buttons
    $("table.blockconfigtable td.itemvalue input[type=submit]").each(function(){
        var name = $(this).attr("name");
        if (name=="cancel") {
            return true; // skip this button
        }
        $(this).click(function(evt){
            var ok = false;
            var rows = $("tr#id_section_activityfilters").nextUntil("tr.sectionheading");
            rows.find("td.itemvalue select").each(function(){
                if ($(this).find("option:selected").text()) {
                    ok = true;
                }
            });
            rows.find("input[type=text]").each(function(){
                if (this.name=="sectiontags") {
                    return true; // skip this element
                }
                if ($(this).val()) {
                    ok = true;
                }
            });
            if (ok==false) {
                alert(TCN.msg["noactivities"]);
                return false;
            }
            if (name=="apply") {
                ok = false;
                $("table.blockconfigtable input[type=checkbox][name^=select_]").each(function(){
                    if ($(this).prop("checked")) {
                        ok = true;
                    }
                });
            }
            if (ok==false) {
                alert(TCN.msg["nosettings"]);
                return false;
            }
            return confirm(TCN.msg["confirm" + name]);
        });
    });
});
