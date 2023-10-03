<x-menu select-page="5">

    {{-- title --}}
    <x-head title="加工指示書CSVアップロード" imageFlg="1"></x-head>

    <div class="row justify-content-center main_height">
        <div class="col-md-8 sub_height">
            <div class="card sub_height">
                <div class="card-body">
                    <form action="{{ route('csv.csv-register') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="border text-center p-3 rounded col-md-10 mx-auto">
                            <div id="file_drag_drop_area">
                                <label>
                                    <p id="text">CSVをアップロード</p>
                                    <input id="file_input" type="file" name="csvFile" />
                                </label>
                            </div>
                        </div>
                        <div class="hide" id="input_text">
                            <label class="maker_label">メーカー</label>
                            <input type="text" placeholder="メーカーを記入してください" name="client_name" class="maker_text">
                        </div>
                        <div class="regist-btn">
                            <input class="submit" type="submit" value="登録">
                        </div>
                        @foreach($errors->all() as $error)
                            <p class="alert-danger">{{$error}}</p>
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-menu>

<script>
    const input_text_area = document.getElementsByClassName("hide");
    input_text_area[0].style.display = "none";
    $(function(){
        // ドラッグしたままエリアに乗った＆外れたとき
        $(document).on('dragover', '#file_drag_drop_area, #file_drag_drop_area_stl', function (event) {
            event.preventDefault();
            $(this).css("background-color", "#d3d3d3");
        });
        $(document).on('dragleave', '#file_drag_drop_area, #file_drag_drop_area_stl', function (event) {
            event.preventDefault();
            $(this).css("background-color", "transparent");
        });

        // ドラッグした時
        $(document).on('drop', '#file_drag_drop_area', function (event) {
            let org_e = event;
            if (event.originalEvent) {
                org_e = event.originalEvent;
            }

            org_e.preventDefault();
            file_input.files = org_e.dataTransfer.files;
            $(this).css("background-color", "transparent");
            let fileName = file_input.files[0]['name'];
            $('#text').text(fileName);
            
            if (fileName) {
                console.log("fileName");
                input_text_area[0].style.display = "";
            }
        });                                                                                                                                                                      
    });
</script>

<style lang="scss" scoped>
    .main_height {
        height: 95%;
    }

    .sub_height {
        height: 100%;
        width: 100%;
        display: table;
    }

    .card {
        background-color: #ffffff;
        padding: 16px;
    }

    .row-head {
        padding: 4px;
        background-color: #6D6D6D;
        color: #ffffff;
        border: 1px solid #dadada;
    }

    .row-detail {
        border-right: 1px solid #dadada;
        padding: 4px 8px;
    }

    .card-body {
        text-align: center;
        display: table-cell;
        vertical-align: middle;
    }

    .plant-label1 {
        padding-right: 100px;
    }

    .border-top {
        border-bottom: 1px solid #dadada;
        border-left: 1px solid #dadada;
    }

    .row-group:nth-child(odd) {
        background-color: #E3E7ED;
    }

    .radio-group {
        padding-bottom: 20px;
    }

    .regist-btn {
        padding-top: 20px;
    }

    #file_drag_drop_area {
        margin: 10px;
        border: 1px #000000 dotted;
        padding: 50px;
    }
    input[type=file] {
        display: none
    }

    .border {
        border-width: 1px;
        width: 350px; 
        margin:  0 auto;
    }

    .maker_text {
        border-width: 1px;
        margin-left: 35px;
        width: 70%;
        font-size: 14px;
        height: 30px;
    }

    #input_text {
        padding-top: 30px;
        width: 350px;
        margin: 0 auto;
        text-align: left
    }

    .submit {
        background: #000000;
        color: #ffffff;
        width: 200px;
        height: 30px;
        border-radius: 30px;
        font-size: 14px;
    }

    .alert-danger {
        padding-top: 10px;
        color: red;
        font-size: 14px;
    }

</style>