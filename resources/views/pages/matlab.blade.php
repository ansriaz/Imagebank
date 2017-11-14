@extends('layouts.app')

@section('title')
  Matlab
@endsection



@section('content')

<style type="text/css">
    .tree_view {
        border:1px solid black;
        width: auto;
        height: 400px;
        max-height: 400px;
        overflow-y: scroll;
        overflow-x: scroll;
    }
    #editor_code {
        border:1px solid black;
        width: auto;height: 400px;
    }
    .editor_code {
        border:1px solid black;
        width: auto;height: 400px;
    }
    .ul {
        display: block; position: static; margin-bottom: 5px; width: 180px;
    }
    .container-full {
        margin: 0 auto;
        width: 100%;
    }
    #noBorder {
        border: none;
        border:0px solid transparent;
    }
    #menu {
        background: #eee;
        padding-top: 5px;
        height: 30px;
    }
    .border_containter{
        border:solid 1px black;
        background: #eee;
    }
    .bg_white{
        background: #fff;
    }
    .nopadding {
       padding: 0 !important;
       margin: 0 !important;
    }
    .nav-pills li.active a:focus, .nav-pills li.active a
    {
        margin-top: -1px;
        margin-bottom: -1px;
    }
    .nav-pills li {
    }
    /*.nav-pills li.active {
        margin-bottom: -1px;
    }*/
    .nav-pills li a
    {
        padding-top: 3px;
        padding-bottom: 3px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-body nopadding">
                <section class="col-md-2 border_containter">
                    <div>
                        <div class="row" id="menu" role="menu" style="height: 35px">
                            {{-- <div class="pull-left">
                                <a type="button" class="noBorder" id="refresh" href="#" style="margin-left: 10px">Project</a> --}}
                                {{-- <i class="glyphicon glyphicon-cog" style="margin-right:5px;"></i> --}}
                            {{-- </div> --}}
                            <div class="container">
                                <ul class="list-inline">
                                    <li style="margin:0; padding:2px"><a type="button" id="save" href="">Save</a></li> |
                                    {{-- <i class="glyphicon glyphicon-floppy-save"></i> --}}
                                    {{-- <li style="margin:0"><a type="button" id="refresh" href="#" class="hidden"><i class="glyphicon glyphicon-refresh"></i></a></li> --}}
                                    <li style="margin:0; padding:2px" class="dropdown">
                                        <a class="dropdown-toggle" data-toggle="dropdown" type="button" href="#">Add</a>
                                        {{-- <i class="glyphicon glyphicon-plus"></i> --}}
                                        <ul class="dropdown-menu">
                                            <li><a href="#" id="addFolder" data-toggle="modal" data-target="#newFileFolderDialog">New Folder</a></li>
                                            <li><a href="#" id="addFile" data-toggle="modal" data-target="#newFileFolderDialog">New File</a></li>
                                            <li style="display:none"><a href="#" id="addProject" data-toggle="modal" data-target="#newFileFolderDialog">New Project</a></li>
                                        </ul>
                                    </li> |
                                    <li style="margin:0; padding:2px"><a type="button" id="search" href="#" data-toggle="modal" data-target="#imageSearchDialog">Images</a></li> 
                                    {{-- <li style="margin:0; padding:2px"><a type="button" id="hideMenu" href="#" class="hidden"><i class="glyphicon glyphicon-triangle-left"></i></a></li> --}}
                                </ul>
                            </div>
                        </div>
                        <div class="row bg_white">
                            <div class="projectTreeView tree_view" id="projectTreeView">
                            </div>
                        </div>
                    </div>
                </section>
                <section class="col-md-10 border_containter nopadding">
                    <div id="" class="">
                        <div class="" id="menu" role="menu" style="height: 35px">
                            <div class="col-md-2">
                                <ul class="list-inline">
                                    <li><a type="button" href="#" id="runMatlab">Run</a></li>
                                    {{-- <i class="glyphicon glyphicon-play" style="margin-right:5px;"></i> --}}
                                    <li><a type="button" href="#" class="hidden">Save All</a></li>
                                    {{-- <i class="glyphicon glyphicon-floppy-save"></i> --}}
                                </ul>
                            </div>
                            <div class="col-md-10 file_tabs">
                                <ul class="nav nav-pills tabs" role="tablists" id="tabs">
                                    <li class="active" id="file"><a data-toggle="tab" href="#tab_file">file</a></li>
                                    {{-- <li><a data-toggle="tab" href="#tabu">Test</a></li> --}}
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content" id="tab_content">
                            <div id="tab_file" class="tab-pane fade in active">
                                <input type="hidden" name="code" id="code">
                                <div id="main" class="col-md-12 nopadding">
                                    <div class="editor_code" id="editor_code"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

{{-- dialog box --}}

<div id="newFileFolderDialog" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Name of New File/Folder</h4>
        </div>
        <form data-toggle="validator" role="form">
            <div class="modal-body has-feedback form-group">
                <p>Write the name of new File/Folder</p>
                <input type="text" class="form-control" id="fileFolderName" placeholder="Name">
                <p class="no_input text-danger hidden">Please enter file/folder name.</p>
                <p class="wrong_ext text-danger hidden">Available file extensions are: <b>.m</b>, <b>.mlx</b>, <b>.mat</b>, <b>.mdl</b>, <b>.slx</b>, <b>.mdlp</b>, <b>.slxp</b>, <b>.mexa64</b>, <b>.mexmaci64</b>, <b>.mexw32</b>, <b>.mexw64</b>, <b>.mlapp</b>, <b>.mlappinstall</b>, <b>.mlpkginstall</b>, <b>.mltbx</b>, <b>.mn</b>, <b>.mu</b>, <b>.p</b></p>
                <p class="wrong_name text-danger hidden">Please use standard alphnumerics only.</p>
            </div>
            <div class="modal-footer">
                <button type="button" id="acceptBtn" class="btn btn-default">OK</button>
            </div>
        </form>
    </div>
  </div>
</div>

<div id="imageSearchDialog" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Select Images</h4>
        </div>
        <form data-toggle="validator" role="form">
            <div class="modal-body has-feedback form-group">
                <p>Search images and paste those search terms here</p>
                <input type="text" class="form-control" id="imagesearch" placeholder="Name">
                <p class="no_input text-danger hidden">Search terms</p>
            </div>
            <div class="modal-footer">
                <button type="button" id="searchImages" class="btn btn-default" data-dismiss="modal">Search Images</button>
            </div>
        </form>
    </div>
  </div>
</div>

@endsection

@section('footer')

    <script src="assets/js/ace/ace.js" type="text/javascript" charset="utf-8"></script>
    <script src="assets/js/ace/theme-eclipse.js" type="text/javascript" charset="utf-8"></script>
    <script src="assets/js/ace/mode-matlab.js" type="text/javascript" charset="utf-8"></script>
    <script>
        var editor_code = ace.edit("editor_code");
        editor_code.setTheme("ace/theme/eclipse");
        var MatlabMode = ace.require("ace/mode/matlab").Mode;
        editor_code.session.setMode(new MatlabMode());
        editor_code.getSession().setUseWrapMode(true);
        editor_code.setShowPrintMargin(false);
        editor_code.setOptions({
            enableBasicAutocompletion: true,
            enableSnippets: true
        });
        // document.getElementById("code").value = editor_code.getValue();
        // editor_code.on("input", function() {
        //     document.getElementById("code").value = editor_code.getValue();
        // });
    </script>

    <script src="assets/js/treeview/bootstrap-treeview.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script>
        $(document).ready(function(){
            $.ajaxSetup ({
                // Disable caching of AJAX responses
                cache: false
            });
            var btnAddClicked = 0;
            var selectedNode = null;
            var _token = $('#token').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            function getTree() {
                var tree = [];
                @foreach ($files as $file)
                    var obj = JSON.parse("{{$file}}".replace(/&quot;/g,'"'));
                    var treeNode = obj;
                    // console.log("outer loop: " + JSON.stringify(treeNode));
                    if(!selectedNode){
                        selectedNode = obj; // for saving the new file under root folder, selectedNode => the root folder to get id
                    }
                    treeNode['text'] = obj['filename'];
                    if(obj['isFolder'] == 1) {
                        treeNode.nodes = [];
                    } else {
                        treeNode.icon = "glyphicon glyphicon-file";
                    };
                    if (obj['parentFileId'] > 0) {
                        var traversal = function (nodeToAdd, dataTree) {
                            for (var a = 0; a < dataTree.length; a++) {
                                curr = dataTree[a];
                                if(curr['id'] == nodeToAdd.parentFileId) {
                                    curr.nodes.push(nodeToAdd);
                                } else {
                                    if(nodeToAdd.parentFileId > 0 && curr.isFolder == 1) {
                                        // console.log("recursive: "+JSON.stringify(nodeToAdd));
                                        // console.log("recursive: "+JSON.stringify(curr.nodes));
                                        return traversal(nodeToAdd, curr.nodes);
                                    }
                                };
                            };
                        };
                        traversal(treeNode, tree);
                    } else {
                        // console.log(treeNode);
                        tree.push(treeNode);
                    };
                @endforeach
                // console.log(tree);
                return tree;
            }
            $('#projectTreeView').treeview({
                expandIcon : "glyphicon glyphicon-folder-close",
                collapseIcon : "glyphicon glyphicon-folder-open",
                // showBorder : false,
                levels: 100,
                data: getTree(),
                // noNodeSelected : function(event, node){
                //     selectedNode = node;
                // }
            });
            // $('#tabs a').click(function (e) {
            //     e.preventDefault();
            //     alert($($(this).attr('href')).index());
            // });

            // ============ set data in tree =============
            function refreshTree (responseData) {
                function getRefreshTree(data) {
                    var tree = [];
                    data.forEach(function(file) {
                        var obj = file;
                        var treeNode = obj;
                        if(!selectedNode){
                            selectedNode = obj; // for saving the new file under root folder, selectedNode => the root folder to get id
                        }
                        treeNode['text'] = obj['filename'];
                        if(obj['isFolder'] == 1) {
                            treeNode.nodes = [];
                        } else {
                            treeNode.icon = "glyphicon glyphicon-file";
                        };
                        if (obj['parentFileId'] > 0) {
                            var traversal = function (nodeToAdd, dataTree) {
                                for (var a = 0; a < dataTree.length; a++) {
                                    curr = dataTree[a];
                                    if(curr['id'] == nodeToAdd.parentFileId) {
                                        curr.nodes.push(nodeToAdd);
                                    } else {
                                        if(nodeToAdd.parentFileId > 0 && curr.isFolder == 1) {
                                            return traversal(nodeToAdd, curr.nodes);
                                        }
                                    };
                                };
                            };
                            traversal(treeNode, tree);
                        } else {
                            tree.push(treeNode);
                        };
                    });
                    // console.log(tree);
                    return tree;
                }
                console.log('[REFRESH]');
                $('#projectTreeView').treeview({
                    expandIcon : "glyphicon glyphicon-folder-close",
                    collapseIcon : "glyphicon glyphicon-folder-open",
                    // showBorder : false,
                    levels: 100,
                    data: getRefreshTree(responseData),
                    noNodeSelected : function(event, node){
                        selectedNode = node;
                    }
                });
            };

            // ============ selected respected node in tree according to tab =========
            $('ul#tabs').on( 'shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
               // activated tab
               // console.log('[ACTIVE_TAB: ]'+e.target);
               // console.log('[ACTIVE_TAB: ]'+$(this).text());
               var node = $('#projectTreeView').treeview('search', [ $(this).text(), {
                                ignoreCase: true,     // case insensitive
                                exactMatch: false,    // like or equals
                                revealResults: true,  // reveal matching nodes
                            }]);
                // console.log(JSON.stringify(node));
                $('#projectTreeView').treeview('selectNode', [ node, { silent: true } ]);
            })
            $('#projectTreeView').on('nodeSelected', function(event, data) {
                if(!data.isFolder) {
                    if($("#tabs li").find('a').attr("href") == "#tab_file") {
                        var anchor = $('.nav-pills a:first');
                        $(anchor.attr('href')).remove();
                        $(anchor).parent().remove();
                    }
                    var tabName = data.filename.split('.')[0];
                    // console.log('[tabName] ' + tabName);
                    var tabs = $('#tabs');
                    var tab_content = $('#tab_content');
                    var isFileOpened = false;
                    $( "#tabs li" ).each(function() {
                        var name = $(this).find('a').attr("href");
                        if(name == ('#tab_')+tabName){
                            isFileOpened = true;
                        };
                    });
                    // console.log('[tabName] ' + tabName);
                    if( isFileOpened ){
                        $('.nav-pills a[href="#tab_'+tabName+'"]').tab('show');
                    } else {
                        tabs.append('<li><a data-toggle="tab" href="#tab_'+tabName+'">'+tabName+'</a></li>');
                        // var tabName = createAceAditor(tabName);
                        tab_content.append('<div id="tab_'+tabName+'" class="tab-pane fade"><div id="main" class="col-md-12 nopadding"><div class="editor_code" id="editor_'+tabName+'"></div></div></div>');
                        $('.nav-pills a:last').tab('show');
                        // $('.nav-pills').tabs('refresh');
                        var editor = ace.edit('editor_'+tabName);
                        editor.setTheme("ace/theme/eclipse");;
                        editor.getSession().setMode("ace/mode/matlab");
                        // editor.resize();
                        // ====== GET file content and set it to editor =======
                        jQuery.get(data.filepath, function(c) {
                            editor.setValue(c);
                        });
                    }
                    selectedNode = data;
                } else {
                    selectedNode = data;
                }
                // console.log('[selectedNode] ' + JSON.stringify(selectedNode));
            });
            $('#addFolder').click(function(e){
                btnAddClicked = 1;
            });
            $('#addFile').click(function(e){
                btnAddClicked = 2;
            });
            $('#addProject').click(function(e){
                btnAddClicked = 3;
            });
            $('#acceptBtn').click(function(e){
                var name = $('#fileFolderName').val().trim();
                var parentFolderId = null;
                if(selectedNode.isFolder != 1){
                    parentFolderId = selectedNode.parentFileId;
                } else {
                    parentFolderId = selectedNode.id;
                }
                if(name) {
                    name = name.replace(/\s/g,'_');
                    console.log(name);
                    if(btnAddClicked == 1) {
                        if(/[~`!#$%\^&*+=\-\[\]\\.';,\/{}|\\":<>\?]/.test(name))
                        {
                            // alert('Kindly add the extension or change the extension to the one used by matlab.');
                            $('.no_input').addClass('hidden');
                            $('.wrong_name').removeClass('hidden');
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            return flase;
                        }
                        var dataToSend = {folder:name, parent:parentFolderId};
                    } else if (btnAddClicked == 2) {
                        var fileExt = ['fig','m','mlx','mat','mdl','slx','mdlp','slxp','mexa64','mexmaci64','mexw32','mexw64','mlapp','mlappinstall','mlpkginstall','mltbx','mn','mu','p'];
                        var found = fileExt.indexOf(name.split('.')[1]) > -1;
                        if(!found)
                        {
                            // alert('Kindly add the extension or change the extension to the one used by matlab.');
                            $('.no_input').addClass('hidden');
                            $('.wrong_ext').removeClass('hidden');
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            return flase;
                        }
                        var dataToSend = {fname:name, parent:parentFolderId};
                    }
                    // console.log('[DATAtoSEND]: ' + JSON.stringify(dataToSend));
                    // ######################### Need to implement ###############################
                    $.ajax({
                        type: "get",
                        url: '/addfile',
                        data: dataToSend,
                        timeout: 5000,
                        success: function(response) {
                            // alert(JSON.stringify(response));
                            refreshTree(response);
                        },
                        error: function(xhr, textStatus, errorThrown){
                            alert(errorThrown);
                        }
                    });
                    $('#newFileFolderDialog').modal('hide');
                } else {
                    $('.no_input').removeClass('hidden');
                    e.preventDefault();
                    // alert('Please enter the name.')
                    return;
                }
                $('#fileFolderName').val('');
                $('.no_input').addClass('hidden');
                $('.wrong_name').addClass('hidden');
                $('.wrong_ext').addClass('hidden');
            });
            $('#refresh').click(function(e){
                $.ajax({
                    type: "get",
                    url: '/refresh',
                    data: "",
                    timeout: 5000,
                    success: function(response) {
                        // alert(JSON.stringify(response));
                        refreshTree(response);
                    },
                    error: function(xhr, textStatus, errorThrown){
                        alert(errorThrown);
                    }
                });
                return;
            });
            $('#hideMenu').click(function(e){
                alert('hideMenu button clicked');
                e.preventDefault();
                return;
            });
            $('#save').click(function(e){

                // ============Get Selected Tab===========
                var activeTab = null;
                // $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                //   activeTab = $(e.target).attr('id');
                // });
                activeTab = $("ul#tabs li.active a").attr('href');
                // alert(JSON.stringify(activeTab.split('tab_')[1]));
                activeTab = activeTab.split('tab_')[1];
                if(activeTab == 'images') {
                    activeTab = activeTab+'.csv';
                }
                //  else {
                //     activeTab = activeTab+'.m';
                // }
                // ==========Get Selected Tab End=========
                var fileid = 0, nod = null;
                // alert(selectedNode.filename.split('.mat')[0]);
                if(activeTab == selectedNode.filename){
                    // console.log('same');
                    if(selectedNode.id > 0){
                        fileid = selectedNode.id;
                        nod = selectedNode;
                    }
                } else {
                    // console.log('not same');
                    var node = $('#projectTreeView').treeview('search', [ activeTab, {
                                    ignoreCase: true,     // case insensitive
                                    exactMatch: false,    // like or equals
                                    revealResults: true,  // reveal matching nodes
                                }]);
                    // console.log(JSON.stringify(node));
                    fileid = node[0].id;
                    nod = node[0];
                }
                var name = nod.filename.split('.')[0];
                // console.log(name);
                var editor = ace.edit('editor_'+name);
                // console.log(editor);
                var dataToSend = {fileid: fileid , content: editor.getValue()};
                console.log("[DATAtoSEND]: " + JSON.stringify(dataToSend));
                $.ajax({
                    type: "post",
                    url: '/savefile',
                    data: dataToSend,
                    timeout: 5000,
                    success: function(response) {
                        var data = JSON.stringify(response);
                        console.log(data);
                        if(data.response) {
                            alert("File has been saved.");
                        }
                    },
                    error: function(xhr, textStatus, errorThrown){
                        alert(errorThrown);
                    }
                });
                e.preventDefault();
                return;
            });
            $('#saveall').click(function(e){
                var fileid = 0;
                if(selectedNode.id > 0){
                    fileid = selectedNode.id;
                }
                // $.ajax({
                //     type: 'post',
                //     url: '/save',
                //     data: {fileid: fileid , content: editor_code.getValue()},
                //     timeout: 5000,
                //     success: function(response) {
                //         alert(JSON.stringify(response));
                //     },
                //     error: function(xhr, textStatus, errorThrown){
                //         alert(errorThrown);
                //     }
                // });
                return;
            });
            $('#searchImages').click(function(e){
                var searchterms = $('#imagesearch').val();
                $.ajax({
                    type: 'get',
                    url: '/matlbimages',
                    data: {searchterm : searchterms},
                    timeout: 20000,
                    success: function(response) {
                        console.log(JSON.stringify(response));
                        alert('Images has been added to your images.csv file. Kindly save the files and refresh page.');
                    },
                    error: function(xhr, textStatus, errorThrown){
                        alert(errorThrown);
                    }
                });
            });
            $('#runMatlab').click(function(e){
                // var searchterms = $('#imagesearch').val();
                // data: {searchterm : searchterms},
                $.ajax({
                    type: 'get',
                    url: '/runmatlab',
                    data : {},
                    timeout: 20000,
                    success: function(response) {
                        // console.log(JSON.stringify(response));
                        if(response.status == 'no'){
                            alert('There is no image in images.csv. Kindly search images by clicking on "Images" button.');
                        } else {
                            alert("Thank you. You will receive result via email. Results can include error if there is any.");
                        }
                    },
                    error: function(xhr, textStatus, errorThrown){
                        alert(errorThrown);
                    }
                });
                // return false;
            });
        });
    </script>

@endsection