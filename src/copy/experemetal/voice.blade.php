<textarea class="_voice" style="width: 100%; height: 200px;">
Значит нужно добавить новое Поле его тип будет checkbox его название будет включить переход в другую сцену его описание будет тут в случае если эта галочка нажата будет осуществлён переход в другую сцену Ну также в поле нужно будет выбрать на какую именно сцену вы хотите переместить игрока его минимальное значение будет 15 его максимальное значение будет Пусть 2.000 точка К добавить новое Поле ну его тип будет it4 ло число добавить новое Поле его тип Select его название будет включить отрисовку сцены его комментарий его описание будет настройки говна чета там хз точка добавить новое Поле его тип Select его название будет включить отрисовку сцены его комментарий его описание будет настройки говна чета там хз   его минимальное значение будет 12 с половиной его максимальное значение будет 505 с половиной
</textarea>

<style>
    .openTag {
        font-weight: 600;
    }
</style>

<div class="col-12 _voiceCodeDiv" style="font-size: 17px;">
    $config = new PropertyConfigStructure($this);


</div>

<script>
    var curentMethod = null;
    var wordsTest = null;
    var wordsTestCursor = -1;
    var wordBlock = "";
    var poleId = 0;
    var lineType = null;
    var prevFraza = "";
    var currentOpenTagE = null;

    function IssetBlock(fraza, isPrev = false) {

        if (isPrev) {
            if (prevFraza == isPrev) {
                wordBlock = "";
                prevFraza = fraza;
                return true;
            }
        }

        fraza = fraza.toLowerCase();
        var _list = fraza.split(" ");
        for (i = 0; i < _list.length; i++) {
            if (wordBlock.indexOf(_list[i]) == -1) return false;
        }
        wordBlock = "";
        prevFraza = fraza;
        RemoveFromCurrentTag(fraza);
        return true;

    }

    function AddMethod(name, opentag, isQt) {
        AddLineText("->" + name + "(");
        if (isQt) AddLineText('"');
        AddOpenTag(opentag);
        if (isQt) AddLineText('"');
        AddLineText(")");
    }

    function AddLineText(txt) {
        var pole = $('.rowCode_' + poleId);
        pole.append(txt);
    }

    function AppendToOpenTag(openTagInd, _word) {
        var e = $(".openTag_" + openTagInd + "_" + poleId);
        currentOpenTagE = e;
        e.append(" " + _word);
    }

    function RemoveFromCurrentTag(text) {
        if (!currentOpenTagE) return;
        var to = currentOpenTagE.text().replace(text, "");

        currentOpenTagE.text(to);
    }

    function AddOpenTag(openTagInd) {
        var pole = $('.rowCode_' + poleId);
        pole.append("<span class='openTag openTag_" + openTagInd + "_" + poleId + "'></span>");
    }

    function NewLine() {
        if(poleId>0){
            AddLineText(";");
        }
        lineType = null;
        poleId += 1;
        $('._voiceCodeDiv').append("<div class='col-12 rowCode_" + poleId + "'> " + "</div>")
        var pole = $('.rowCode_' + poleId);
        pole.text("$config");
    }

    function addWord(word) {
        // console.log(word);
        word = word.toLowerCase();
        wordBlock += " " + word;

        if (IssetBlock("checkbox") && prevFraza=="добавить новое поле") {
            AddMethod("Checkbox", "typeName");
        }

        if (IssetBlock("добавить новое поле")) {
            NewLine();
        }
        if (IssetBlock("его тип Select")) {
            AddMethod("Select", "typeName");
        }
        if (IssetBlock("его тип строка")) {
            AddMethod("String", "typeName");
        }
        if (IssetBlock("его тип число")) {
            AddMethod("Int", "typeName");
        }

        if (IssetBlock("его тип bool") || IssetBlock("его тип checkbox") || IssetBlock("тип будет checkbox")) {
            AddMethod("Checkbox", "typeName");
        }

        if (IssetBlock("название будет")) {
            AddMethod("SetLabel", "label", true);
            return;
        }

        if (prevFraza == "название будет") {
            AppendToOpenTag("label", word);
        }


        if (IssetBlock("его описание будет")) {
            AddMethod("SetDescr", "descr", true);
            return;
        }

        if (prevFraza == "его описание будет") {
            AppendToOpenTag("descr", word);
        }

        if (IssetBlock("его минимальное значение будет")) {
            AddMethod("SetMin", "SetMin", false);
            return;
        }

        if (prevFraza == "его минимальное значение будет") {
            var w = parseInt(word);
            if(w) {
                AppendToOpenTag("SetMin", w);
            }
        }


        if (IssetBlock("его максимальное значение будет")) {
            AddMethod("SetMax", "SetMax", false);
            return;
        }

        if (prevFraza == "его максимальное значение будет") {
            var w = parseInt(word);
            if(w) {
                AppendToOpenTag("SetMax", w);
            }
        }


        IssetBlock("точка");
        IssetBlock("скобка закрывает");
        IssetBlock("стоп");

        //  console.log(wordBlock);
    }

    function readTestLoop() {
        wordsTestCursor += 1;
        if (wordsTest.length + "" == wordsTestCursor + "") {
            console.log("EE");
            return;
        }
        addWord(wordsTest[wordsTestCursor]);
        setTimeout(readTestLoop, 10);
    }

    function readTest() {
        var text = $('._voice').val();
        wordsTest = text.split(" ");

        readTestLoop();

    }


    $(document).ready(function () {
        readTest();
    });
</script>
