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
            $(img).click(function(){
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
});
