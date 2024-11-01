;
(function ( $ ) {
    "use strict"

    document.addEventListener('DOMContentLoaded', (event) =>
    {

        if( document.querySelector("#ymc-crossword-container") )
        {

            let board;
            let wordArr;
            let wordBank;
            let wordsActive;
            let boardMap;
            let focusChar;
            let focusIndex = null;
            let mode;
            let wordElementsAcross;
            let wordElementsDown;

            let Bounds = {
                top:0,
                right:0,
                bottom:0,
                left:0,

                Update: function( x, y ) {
                    this.top = Math.min( y, this.top );
                    this.right = Math.max( x, this.right );
                    this.bottom = Math.max( y, this.bottom );
                    this.left = Math.min( x, this.left );
                },

                Clean: function(){
                    this.top = 999;
                    this.right = 0;
                    this.bottom = 0;
                    this.left = 999;
                }
            };

            const Timer = {
                totalSeconds: 0,
                start: function ( crossword ) {
                    let self = this;
                    let mode_timer = true;
                    let keys_timer = Object.keys(sessionStorage);
                    for(let key of keys_timer) {
                        if(sessionStorage.getItem(key) === '_t_s') {
                            mode_timer = false;
                        }
                    }
                    if( mode_timer ) {
                        sessionStorage.setItem('_ymc_timer', '_t_s');

                        function pad(val) {
                            return val > 9 ? val : "0" + val;
                        }
                        this.interval = setInterval(function () {
                            self.totalSeconds += 1;
                            crossword.find('.ymc-control-panel .ymc-timer .ymc-min, .ymc-popup-crossword .ymc-game-over .ymc-min').
                                text(pad(Math.floor(self.totalSeconds / 60 % 60)));
                            crossword.find('.ymc-control-panel .ymc-timer .ymc-sec, .ymc-popup-crossword .ymc-game-over .ymc-sec').
                                text(pad(parseInt(self.totalSeconds % 60)));

                        }, 1000);
                    }
                },
                reset: function ( crossword ) {
                    Timer.totalSeconds = null;
                    clearInterval(this.interval);
                    crossword.find('.ymc-control-panel .ymc-timer .ymc-min, .ymc-popup-crossword .ymc-game-over .ymc-min').text("00");
                    crossword.find('.ymc-control-panel .ymc-timer .ymc-sec, .ymc-popup-crossword .ymc-game-over .ymc-sec').text("00");
                },
                pause: function () {
                    clearInterval(this.interval);
                    delete this.interval;
                }
            };

            const crossword = $(document.querySelector('#ymc-crossword-container'));


            // MAIN FUNC
            function Play() {
                let charEleArr = document.querySelectorAll('#ymc-crossword-container .ymc-square .ymc-char');
                mode = 0;

                for(var i = 0; i < charEleArr.length; i++){
                    RegisterChar(charEleArr[i],boardMap[i]);
                    charEleArr[i].placeholder = "";
                }

                FormatClues();
            }

            function FormatClues() {
                let cluesAcross = document.querySelector("#ymc-crossword-container #ymc-cluesAcross"),
                    cluesDown = document.querySelector("#ymc-crossword-container #ymc-cluesDown"),
                    directionAcross = document.querySelector("#ymc-crossword-container #ymcDirectionAcross"),
                    directionDown = document.querySelector("#ymc-crossword-container #ymcDirectionDown");

                if( cluesAcross !== null && cluesDown !== null ) {

                    cluesAcross.innerHTML = "";
                    cluesDown.innerHTML = "";

                    cluesAcross.appendChild(directionAcross);
                    cluesDown.appendChild(directionDown);

                    for(let i = 0; i < wordElementsAcross.length; i++){
                        let lineEle = cluesAcross.appendChild(wordElementsAcross[i].ele),
                            numEle = lineEle.getElementsByClassName("ymc-lineNum")[0],
                            linkEle = lineEle.getElementsByClassName("ymc-clue")[0];

                        numEle.innerHTML = wordElementsAcross[i].num;
                        linkEle.setAttribute('data-num', wordElementsAcross[i].num );
                        linkEle.setAttribute('data-dir', 0 );
                        RemoveClass(numEle,'disabled');
                    }

                    for(let i = 0; i < wordElementsDown.length; i++){
                        let lineEle = cluesDown.appendChild(wordElementsDown[i].ele),
                            numEle = lineEle.getElementsByClassName("ymc-lineNum")[0],
                            linkEle = lineEle.getElementsByClassName("ymc-clue")[0];
                        numEle.innerHTML = wordElementsDown[i].num;
                        linkEle.setAttribute('data-num', wordElementsDown[i].num );
                        linkEle.setAttribute('data-dir', 1 );
                        RemoveClass(numEle,'disabled');
                    }
                }

            }

            function Generate() {
                wordElementsAcross = [];
                wordElementsDown = [];

                mode = 1;
                GetWordsFromInput();

                for(var i = 0, isSuccess=false; i < 19 && !isSuccess; i++){
                    CleanVars();
                    isSuccess = PopulateBoard();
                }

                let crossword = document.querySelector('#ymc-crossword-container');
                $(crossword).find('.ymc-crossword').html( (isSuccess) ? BoardToHtml(" ") : "<div class='ymc-crossword-notice'>Failed to create crossword puzzle</div>" );

                FormatClues();
            }

            function GetWordsFromInput(){
                wordArr = [];
                for(var i=0, val, w=document.querySelectorAll("#ymc-crossword-container .ymc-line"); i<w.length; i++){
                    val = w[i].getElementsByClassName("ymc-word")[0].value.toUpperCase();
                    if (val !== null && val.length > 1){wordArr.push({ele:w[i],value:val});}
                }
            }

            function CleanVars(){
                Bounds.Clean();
                wordBank = [];
                wordsActive = [];
                board = [];

                for(var i = 0; i < 50; i++){
                    board.push([]);
                    for(var j = 0; j < 50; j++){
                        board[i].push({value:null,char:[]});
                    }
                }
            }

            function PopulateBoard(){
                PrepareBoard();
                for(var i=0,isOk=true,len=wordBank.length; i<len && isOk; i++){
                    isOk = AddWordToBoard();
                }
                return isOk;
            }

            function PrepareBoard(){
                wordBank=[];

                for(var i = 0, len = wordArr.length; i < len; i++){
                    wordBank.push(new WordObj(wordArr[i]));
                }

                for(i = 0; i < wordBank.length; i++){
                    for(var j = 0, wA=wordBank[i]; j<wA.char.length; j++){
                        for(var k = 0, cA=wA.char[j]; k<wordBank.length; k++){
                            for(var l = 0,wB=wordBank[k]; k!==i && l<wB.char.length; l++){
                                wA.totalMatches += (cA === wB.char[l])?1:0;
                            }
                        }
                    }
                }
            }

            function AddWordToBoard(){
                let i, len, curIndex, curWord, curChar, curMatch, testWord, testChar, minMatchDiff = 9999, curMatchDiff;

                if(wordsActive.length < 1){
                    curIndex = 0;
                    for(i = 0, len = wordBank.length; i < len; i++){
                        if (wordBank[i].totalMatches < wordBank[curIndex].totalMatches){
                            curIndex = i;
                        }
                    }
                    wordBank[curIndex].successfulMatches = [{x:12,y:12,dir:0}];
                }

                else{
                    curIndex = -1;

                    for(i = 0, len = wordBank.length; i < len; i++){
                        curWord = wordBank[i];
                        curWord.effectiveMatches = 0;
                        curWord.successfulMatches = [];
                        for(var j = 0, lenJ = curWord.char.length; j < lenJ; j++){
                            curChar = curWord.char[j];
                            for (var k = 0, lenK = wordsActive.length; k < lenK; k++){
                                testWord = wordsActive[k];
                                for (var l = 0, lenL = testWord.char.length; l < lenL; l++){
                                    testChar = testWord.char[l];
                                    if (curChar === testChar){
                                        curWord.effectiveMatches++;
                                        var curCross = {x:testWord.x,y:testWord.y,dir:0};
                                        if(testWord.dir === 0){
                                            curCross.dir = 1;
                                            curCross.x += l;
                                            curCross.y -= j;
                                        }
                                        else{
                                            curCross.dir = 0;
                                            curCross.y += l;
                                            curCross.x -= j;
                                        }

                                        var isMatch = true;

                                        for(var m = -1, lenM = curWord.char.length + 1; m < lenM; m++){
                                            var crossVal = [];
                                            if (m !== j){
                                                if (curCross.dir === 0){
                                                    var xIndex = curCross.x + m;

                                                    if (xIndex < 0 || xIndex > board.length){
                                                        isMatch = false;
                                                        break;
                                                    }

                                                    try {
                                                        if( board[xIndex][curCross.y] !== undefined ) {
                                                            crossVal.push(board[xIndex][curCross.y].value);
                                                            crossVal.push(board[xIndex][curCross.y + 1].value);
                                                            crossVal.push(board[xIndex][curCross.y - 1].value);
                                                        }
                                                    }
                                                    catch (e) {
                                                        //console.error(e);
                                                    }

                                                }
                                                else{
                                                    var yIndex = curCross.y + m;

                                                    if (yIndex < 0 || yIndex > board[curCross.x].length){
                                                        isMatch = false;
                                                        break;
                                                    }

                                                    try {
                                                        if( board[curCross.x][yIndex] !== undefined ) {
                                                            crossVal.push(board[curCross.x][yIndex].value);
                                                            crossVal.push(board[curCross.x + 1][yIndex].value);
                                                            crossVal.push(board[curCross.x - 1][yIndex].value);
                                                        }
                                                    }
                                                    catch (e) {
                                                        //console.error(e);
                                                    }


                                                }

                                                if(m > -1 && m < lenM-1){
                                                    if (crossVal[0] !== curWord.char[m]){
                                                        if (crossVal[0] !== null){
                                                            isMatch = false;
                                                            break;
                                                        }
                                                        else if (crossVal[1] !== null){
                                                            isMatch = false;
                                                            break;
                                                        }
                                                        else if (crossVal[2] !== null){
                                                            isMatch = false;
                                                            break;
                                                        }
                                                    }
                                                }
                                                else if (crossVal[0] !== null){
                                                    isMatch = false;
                                                    break;
                                                }
                                            }
                                        }

                                        if (isMatch === true){
                                            curWord.successfulMatches.push(curCross);
                                        }
                                    }
                                }
                            }
                        }

                        curMatchDiff = curWord.totalMatches - curWord.effectiveMatches;

                        if (curMatchDiff<minMatchDiff && curWord.successfulMatches.length>0){
                            curMatchDiff = minMatchDiff;
                            curIndex = i;
                        }
                        else if (curMatchDiff <= 0){
                            return false;
                        }
                    }
                }

                if (curIndex === -1){
                    return false;
                }

                let spliced = wordBank.splice(curIndex, 1);
                wordsActive.push(spliced[0]);

                let pushIndex = wordsActive.length - 1,
                    rand = Math.random(),
                    matchArr = wordsActive[pushIndex].successfulMatches,
                    matchIndex = Math.floor(rand * matchArr.length),
                    matchData = matchArr[matchIndex];

                wordsActive[pushIndex].x = matchData.x;
                wordsActive[pushIndex].y = matchData.y;
                wordsActive[pushIndex].dir = matchData.dir;


                let prevObj = null;

                for(i = 0, len = wordsActive[pushIndex].char.length; i < len; i++){
                    var cObj,
                        cIndex,
                        xInd = matchData.x,
                        yInd = matchData.y;

                    if (matchData.dir === 0){ xInd = matchData.x + i; }
                    else{ yInd = matchData.y + i; }

                    cObj = {wordIndex:pushIndex,prev:prevObj,
                        value:wordsActive[pushIndex].char[i],next:null};

                    cIndex = board[xInd][yInd].char.length;

                    board[xInd][yInd].char.push(cObj);
                    board[xInd][yInd].value = wordsActive[pushIndex].char[i];

                    Bounds.Update(xInd,yInd);

                    if (prevObj !== null){
                        prevObj.next = board[xInd][yInd].char[cIndex];
                    }

                    prevObj = board[xInd][yInd].char[cIndex];
                }

                prevObj = null;
                return true;

            }

            function BoardToHtml( blank ){

                boardMap = [];

                for(var i=Bounds.top-1, str=""; i<Bounds.bottom+2; i++){ //y
                    str += "<div class='ymc-row'>";
                    for(var j=Bounds.left-1; j<Bounds.right+2; j++){ //x
                        str += BoardCharToElement(board[j][i],j,i);
                    }
                    str += "</div>";
                }

                return str;
            }

            function BoardCharToElement( c,x,y ){
                let inner = "";
                let num = "";
                let dataAtr = [];

                if (c.value !== null) {

                    for(var i=0 ; i < c.char.length; i++) {
                        c.char[i].index = boardMap.length;
                        if (c.char[i].prev === null){
                            var matchingObj = wordsActive[c.char[i].wordIndex];

                            if (num === ""){
                                num = wordElementsDown.length + wordElementsAcross.length + 1;
                            }
                            if (matchingObj.dir === 0){
                                wordElementsAcross.push({num:num,ele:matchingObj.element});
                            }
                            else{
                                wordElementsDown.push({num:num,ele:matchingObj.element});
                            }
                        }
                    }
                    boardMap.push(c);

                    inner = EleStr('input', [{a:'type',v:['text']},
                            {a:'class',v:['ymc-char']},
                            {a:'maxlength',v:['1']},
                            {a:'data-letter',v:[c.value]},
                            {a:'placeholder',v:[c.value]}],
                        EleStr('span', [{a:'class',v:['ymc-num']}],num));

                    dataAtr.push({a:'data-x',v:[x]});
                    dataAtr.push({a:'data-y',v:[y]});
                    dataAtr.push({a:'data-c',v:[num]});
                }

                let firstLetter = ( num !== "" ) ? " ymc-first-letter" : "";
                let el = ( num !== "" ) ? num - 1 : "";
                dataAtr.push({a:'class',v:['ymc-square'+firstLetter+'']});
                dataAtr.push({a:'data-el',v:[el]});

                return EleStr('div',dataAtr,inner);
            }

            // OBJECT DEFINITIONS
            function WordObj( wordData ){
                this.element = wordData.ele;
                this.string = wordData.value;
                this.char = wordData.value.split("");
                this.totalMatches = 0;
                this.effectiveMatches = 0;
                this.successfulMatches = [];
            }

            // EVENTS
            function RegisterChar( ele, boardChar ) {

                let BoardCharClick = function  ( boardChar ) {

                    return function() {

                        if ( mode === 1 ) {
                            return;
                        }

                        this.select();

                        // wordsActive | wordIndex
                        // boardMap | index

                        if( $(window).width() > 1024 ) {

                            let word = '';
                            let word2 = '';

                            crossword.find('.ymc-clueBlock').find('.ymc-clue').removeClass('ymc-active');

                            if ( boardChar.char.length === 1 ) {
                                word = wordsActive[boardChar.char[0].wordIndex].string;
                                let actWord = crossword.find('.ymc-clueBlock .ymc-word[value="'+ word +'"]').
                                siblings('.ymc-clue').addClass('ymc-active');
                                actWord[0].scrollIntoView({block: "center", behavior: "smooth"});
                            }
                            else {
                                word = wordsActive[boardChar.char[0].wordIndex].string;
                                word2 = wordsActive[boardChar.char[1].wordIndex].string;
                                crossword.find('.ymc-clueBlock .ymc-word[value="'+ word +'"], .ymc-clueBlock .ymc-word[value="'+ word2 +'"]').
                                siblings('.ymc-clue').each(function() {
                                    $(this).addClass('ymc-active');
                                });
                            }
                        }

                        /*if ( boardChar.char.length > 1 ) {
                            if ( focusIndex >= boardChar.char.length-1 ){
                                focusIndex = 0;
                            }
                            else {
                                focusIndex++;
                            }
                        }*/
                    }
                }
                ele.addEventListener('click', BoardCharClick(boardChar));

                let BoardCharFocus = function( boardChar ) {
                    return function () {

                        if (mode === 1){
                            return;
                        }

                        if (!(boardChar.char[focusIndex] && boardChar.char[focusIndex].prev === focusChar)){
                            focusIndex = Math.max(0,boardChar.char.indexOf(focusChar));
                        }

                        this.onkeydown = function(e){
                            if (mode === 1){
                                return;
                            }
                            let key = e.keyCode || e.which;

                            if (key === 8) {
                                if (boardChar.char[focusIndex].prev !== null) {
                                    focusChar = boardChar.char[focusIndex].prev;

                                    //let isEnd = (boardChar.char[focusIndex].next !== null) ? true : false;
                                    document.getElementsByClassName('ymc-char')
                                        [boardChar.char[focusIndex].prev.index].focus();

                                    /*if (isEnd) {
                                            document.getElementsByClassName('ymc-char')
                                                [boardChar.char[focusIndex].index].value = "";
                                            document.getElementsByClassName('ymc-char')
                                                [boardChar.char[focusIndex].next.index].value = "";
                                    }*/
                                }
                            }
                        }

                        this.onkeypress = function(){
                            if (mode === 1){
                                return;
                            }
                            if (boardChar.char[focusIndex].next !== null){
                                focusChar = boardChar.char[focusIndex].next;
                                document.getElementsByClassName('ymc-char')
                                    [boardChar.char[focusIndex].next.index].focus();
                            }
                        }
                    }
                }
                //ele.addEventListener('focus', BoardCharFocus(boardChar));

                let KeyUpChar = function( boardChar ) {
                    return function(e) {
                        // wordsActive | wordIndex
                        // boardMap | index

                        let key = e.keyCode || e.which;

                        // X
                        if ( boardChar.char.length === 1 ) {
                            if( boardChar.char[0].next !== null && key !== 8  && key !== 46 ) {
                                document.getElementsByClassName('ymc-char')
                                    [boardChar.char[0].next.index].focus();
                            }
                        }
                        // Y
                        else {
                            if( boardChar.char[1].next !== null && key !== 8  && key !== 46 ) {
                                document.getElementsByClassName('ymc-char')
                                    [boardChar.char[1].next.index].focus();
                            }
                            // else {
                            //     document.getElementsByClassName('ymc-char')
                            //         [boardChar.char[0].next.index].focus();
                            // }
                        }

                        if( key === 8 ) {
                            if ( boardChar.char[0].prev !== null ) {
                                document.getElementsByClassName('ymc-char')
                                    [boardChar.char[0].prev.index].focus();

                            }
                        }
                    }
                }
                ele.addEventListener('keyup', KeyUpChar(boardChar));
            }

            // HELPER FUNCTIONS
            function EleStr( e,c,h ){
                h = (h)?h:"";
                for(var i=0,s="<"+e+" "; i<c.length; i++){
                    s+=c[i].a+ "='"+ArrayToString(c[i].v," ")+"' ";
                }
                return (s+">"+h+"</"+e+">");
            }

            function ArrayToString( a,s ){
                if(a===null || a.length<1) return "";
                if(s===null) s=",";
                for(var r=a[0],i=1;i<a.length;i++){r+=s+a[i];}
                return r;
            }

            function AddClass( ele,classStr ){
                ele.className = ele.className.replaceAll(' '+classStr,'')+' '+classStr;
            }

            function RemoveClass( ele,classStr ){
                ele.className = ele.className.replaceAll(' '+classStr,'');
            }

            function ToggleClass( ele,classStr ){
                var str = ele.className.replaceAll(' '+classStr,'');
                ele.className = (str.length===ele.className.length)?str+' '+classStr:str;
            }

            function checkCrossword( crossword ) {
                let modeCheckCrossword = true;
                crossword.find(".ymc-square .ymc-char").each(function() {
                    if ( !($(this).attr('data-letter') === ($(this).val().toUpperCase())) ) {
                        modeCheckCrossword = false;
                    }
                });
                return modeCheckCrossword;
            }

            function clearCrossword( crossword ) {
                crossword.find(".ymc-square .ymc-char").each(function() {
                     $(this).val('');
                });
                crossword.find(".ymc-square").each(function() {
                    $(this).removeClass('ymc-correct');
                });
                crossword.find(".ymc-crossword-panel .ymc-clue").each(function() {
                    $(this).removeClass('ymc-completed');
                });
            }

            function audioSound( sound ) {
                let src = '';
                switch (sound) {
                    case 'ready' : src = _ymc_crossword_object.path + '/assets/media/ready.mp3'; break;
                    case 'error' : src = _ymc_crossword_object.path + '/assets/media/error.mp3'; break;
                    case 'win'   : src = _ymc_crossword_object.path + '/assets/media/win.mp3'; break;
                }
                let audio = new Audio();
                audio.src = src;
                audio.autoplay = true;
            }

            function OverflowScreen() {

                let countCharsX = (Bounds.right + 2) - (Bounds.left - 1);
                let widthCrossword = (countCharsX * 30) + (countCharsX * 2);

                if( widthCrossword > crossword.find('.ymc-crossword').width() ) {
                    crossword.find('.ymc-crossword').addClass('ymc-scrollX');
                    crossword.find('.ymc-crossword .ymc-row').css({'minWidth':widthCrossword+'px'});
                }
            }

            function ClearSessionStorage() {
                sessionStorage.clear();
            }

            function ProgressBar() {
                crossword.find('.ymc-progress-bar .ymc-total-words').html(wordArr.length);
                crossword.find('.ymc-progress-bar .ymc-completed-words').html(
                crossword.find('.ymc-clueBlock .ymc-line .ymc-completed').length);
            }

            function ScoreCrossword( counter, letter ) {

                // wordsActive | wordIndex
                // boardMap | index

                //console.log(letter);
                //console.log('wordArr: ', wordArr);
                //console.log('wordsActive:', wordsActive);
                //console.log('boardMap', boardMap);

                //let letterW = letter;
                // boardMap.forEach((el) => {
                //     if( el.value === letter ) {
                //         console.log(el.value);
                //         el.char.forEach((ch) => {
                //             //console.log(ch.wordIndex);
                //             console.log(wordsActive[ch.wordIndex].x);
                //             console.log(wordsActive[ch.wordIndex].y);
                //         })
                //     }
                // });

                if( sessionStorage.getItem('incorrect_answers') )
                {
                    let iaMap = new Map(JSON.parse(sessionStorage.getItem('incorrect_answers')));

                    //console.log(counter);

                    for (let item of iaMap.keys())
                    {

                        if( item === counter )
                        {
                            iaMap.get(item).push(1);

                            iaMap.set(counter, iaMap.get(item));

                            sessionStorage.setItem('incorrect_answers', JSON.stringify(Array.from(iaMap.entries())));

                            console.log(iaMap.get(item));
                        }
                    }
                }
                else
                {
                    let incorrectMap = new Map();
                    incorrectMap.set(counter, [1]);
                    sessionStorage.setItem('incorrect_answers', JSON.stringify(Array.from(incorrectMap.entries())));
                }

            }

            String.prototype.replaceAll = function (replaceThis, withThis) {
                let re = new RegExp(replaceThis,"g");
                return this.replace(re, withThis);
            };


            // INITIAL LOAD
            ClearSessionStorage();
            Generate();
            OverflowScreen();
            ProgressBar();
            Play();


            // ADDITIONAL FUNCTIONS
            crossword.find('.ymc-char').hover(
                function () {
                    if( $(this).val() !== "" ) {
                        $(this).css({'cursor': 'pointer'});
                    } else {
                        $(this).css({'cursor': 'text'});
                    }
                },
                function () {}
            );

            crossword.find('.ymc-char').on("keyup", function (e) {

                let isNextElem = $(this).closest('.ymc-square').next('[data-c]');
                let isPrevElem = $(this).closest('.ymc-square').prev('[data-c]');
                let isParentElem = $(this).closest('.ymc-square');
                let mute = crossword.data('mute');
                let popup = crossword.data('popup');

                // Move cursor
                if( e.key === "ArrowRight" ) {
                    if( isNextElem.length > 0 ) {
                        isNextElem.find('input').focus().select();
                    }
                }
                if( e.key === "ArrowLeft" ) {
                    if( isPrevElem.length > 0 ) {
                        isPrevElem.find('input').focus().select();
                    }
                }
                if( e.key === "ArrowDown" ) {
                    let x = isParentElem.attr('data-x');
                    isParentElem.closest('.ymc-row').next('.ymc-row').find('[data-x="'+x+'"] input').focus().select();
                }
                if( e.key === "ArrowUp" ) {
                    let x = isParentElem.attr('data-x');
                    isParentElem.closest('.ymc-row').prev('.ymc-row').find('[data-x="'+x+'"] input').focus().select();
                }

                crossword.find('.ymc-square').removeClass('ymc-correct ymc-error ymc-highlighted-line').find('.ymc-num').show();
                crossword.find('.ymc-clueBlock .ymc-clue').removeClass('ymc-completed ymc-clue-error ymc-active');

                crossword.find('.ymc-square.ymc-first-letter').each(function (i, elem) {
                    let el_i = $(elem)[0].dataset.el;
                    let arr_el = el_i.split(',');
                    for (let v = 0; v < arr_el.length; v++) {
                        let el = arr_el[v];
                        let arr = wordsActive[el];
                        let arr_char = arr.char;
                        let dir = arr.dir;
                        let x = arr.x;
                        let y = arr.y;
                        let c = $(elem)[0].dataset.c;
                        let df = '';
                        let flag_empty = '';
                        let flag_correct = true;

                        if ( dir === 0 ) {  df = x; }
                        else {  df = y; }

                        // Сheck for character matches
                        // Horizontally
                        if (dir === 0) {
                            let index = 0;
                            for (let z = df; z < arr_char.length + df; z++) {
                                let it = crossword.find('.ymc-square[data-x="' + z + '"][data-y="' + y + '"] .ymc-char').val().toUpperCase();
                                if (it) {
                                    if ( it !== arr_char[index] ) {
                                        flag_correct = false;
                                    }
                                }
                                else {
                                    flag_empty = 'empty';
                                }
                                index++;
                            }
                        }

                        // Сheck for character matches
                        // Vertically
                        else {
                            let index = 0;
                            for (let z = df; z < arr_char.length + df; z++) {
                                let it = crossword.find('.ymc-square[data-y="' + z + '"][data-x="' + x + '"] .ymc-char').val().toUpperCase();
                                if (it) {
                                    if (it !== arr_char[index]) {
                                        flag_correct = false;
                                    }
                                }
                                else {
                                    flag_empty = 'empty';
                                }
                                index++;
                            }
                        }

                        // Correct Word
                        if ( flag_empty !== 'empty' && flag_correct === true ) {

                            // Horizontally
                            if (dir === 0) {

                                let counterElem = crossword.find('.ymc-square[data-x="' + df + '"][data-y="' + y + '"]');
                                let counter = counterElem.data('c');
                                crossword.find('.ymc-clueBlock .ymc-clue[data-num="'+ counter +'"][data-dir="0"]').addClass('ymc-completed');

                                if( mute ) {
                                    sessionStorage.removeItem('w_x_e_h_'+counter);
                                    if(Object.keys(sessionStorage).length === 0) {
                                        sessionStorage.setItem('w_x_h_'+counter, 'x_h_'+counter);
                                        audioSound('ready');
                                    }
                                    else {
                                        let mode_key = true;
                                        let keys = Object.keys(sessionStorage);
                                        for(let key of keys) {
                                            if(sessionStorage.getItem(key) === 'x_h_'+counter) {
                                                mode_key = false;
                                            }
                                        }
                                        if( mode_key ) {
                                            sessionStorage.setItem('w_x_h_'+counter, 'x_h_'+counter);
                                            audioSound('ready');
                                        }
                                    }
                                }

                                for (let z = df; z < arr_char.length + df; z++) {
                                    crossword.find('.ymc-square[data-x="' + z + '"][data-y="' + y + '"]').addClass('ymc-correct');
                                }
                            }

                            // Vertically
                            else {

                                let counterElem = crossword.find('.ymc-square[data-y="' + df + '"][data-x="' + x + '"]');
                                let counter = counterElem.data('c');
                                crossword.find('.ymc-clueBlock .ymc-clue[data-num="'+ counter +'"][data-dir="1"]').addClass('ymc-completed');

                                if( mute ) {
                                    sessionStorage.removeItem('w_y_e_v_'+counter);
                                    if(Object.keys(sessionStorage).length === 0) {
                                        sessionStorage.setItem('w_y_v_'+counter, 'y_v_'+counter);
                                        audioSound('ready');
                                    }
                                    else {
                                        let mode_key = true;
                                        let keys = Object.keys(sessionStorage);
                                        for( let key of keys ) {
                                            if( sessionStorage.getItem(key) === 'y_v_'+counter ) {
                                                mode_key = false;
                                            }
                                        }
                                        if( mode_key ) {
                                            sessionStorage.setItem('w_y_v_'+counter, 'y_v_'+counter);
                                            audioSound('ready');
                                        }
                                    }
                                }

                                for (let z = df; z < arr_char.length + df; z++) {
                                    crossword.find('.ymc-square[data-y="' + z + '"][data-x="' + x + '"]').addClass('ymc-correct');
                                }
                            }
                        }

                        // Incorrect Word
                        if ( flag_empty !== 'empty' && flag_correct === false ) {

                            // Horizontally
                            if ( dir === 0 ) {

                                let counterElem = crossword.find('.ymc-square[data-x="' + df + '"][data-y="' + y + '"] ');
                                let counter = counterElem.data('c');
                                crossword.find('.ymc-clueBlock .ymc-clue[data-num="'+ counter +'"][data-dir="0"]').addClass('ymc-clue-error');

                                if( mute ) {
                                    sessionStorage.removeItem('w_x_h_'+counter);
                                    if(Object.keys(sessionStorage).length === 0) {
                                        sessionStorage.setItem('w_x_e_h_'+counter, 'x_e_h_'+counter);
                                        audioSound('error');
                                    }
                                    else {
                                        let mode_key = true;
                                        let keys = Object.keys(sessionStorage);
                                        for(let key of keys) {
                                            if(sessionStorage.getItem(key) === 'x_e_h_'+counter) {
                                                mode_key = false;
                                            }
                                        }
                                        if(mode_key) {
                                            sessionStorage.setItem('w_x_e_h_'+counter, 'x_e_h_'+counter);
                                            audioSound('error');
                                        }
                                    }
                                }

                                for (let z = df; z < arr_char.length + df; z++) {
                                    crossword.find('.ymc-square[data-x="' + z + '"][data-y="' + y + '"] ').addClass('ymc-error');
                                }
                            }

                            // Vertically
                            else {

                                let counterElem = crossword.find('.ymc-square[data-y="' + df + '"][data-x="' + x + '"] ');
                                let counter = counterElem.data('c');
                                crossword.find('.ymc-clueBlock .ymc-clue[data-num="'+ counter +'"][data-dir="1"]').addClass('ymc-clue-error');

                                if( mute ) {
                                    sessionStorage.removeItem('w_y_v_'+counter);
                                    if(Object.keys(sessionStorage).length === 0) {
                                        sessionStorage.setItem('w_y_e_v_'+counter, 'y_e_v_'+counter);
                                        audioSound('error');
                                    }
                                    else {
                                        let mode_key = true;
                                        let keys = Object.keys(sessionStorage);
                                        for(let key of keys) {
                                            if(sessionStorage.getItem(key) === 'y_e_v_'+counter) {
                                                mode_key = false;
                                            }
                                        }
                                        if(mode_key) {
                                            sessionStorage.setItem('w_y_e_v_'+counter, 'y_e_v_'+counter);
                                            audioSound('error');
                                        }
                                    }
                                }

                                for (let z = df; z < arr_char.length + df; z++) {
                                    crossword.find('.ymc-square[data-y="' + z + '"][data-x="' + x + '"] ').addClass('ymc-error');
                                }
                            }
                        }
                    }
                });

                // Run Timer
                Timer.start( crossword );
                crossword.find('.ymc-actions .ymc-button').removeClass('active');

                ProgressBar();

                // If Game Out
                if(checkCrossword( crossword )) {

                    Timer.pause( crossword );

                    if( popup ) {
                        crossword.find('.ymc-popup-crossword').show();
                        //clearCrossword(crossword);
                    }
                    if( mute ) {
                        sessionStorage.clear();
                        audioSound('win');
                    }
                }
            });

            crossword.find('.ymc-popup-crossword .ymc-crossword-btn-close').on('click', function (){
                crossword.find('.ymc-popup-crossword').hide();
            });

            crossword.find('.ymc-clueBlock .ymc-line .ymc-clue').on("click", function (e) {
                e.preventDefault();
                let num = $(this).data('num');
                let dir = $(this).data('dir');
                $(this).
                    addClass('ymc-active').
                    closest('.ymc-line').
                    siblings('.ymc-line').
                    find('.ymc-clue').
                    removeClass('ymc-active').
                    closest('.ymc-col').
                    siblings('.ymc-col').
                    find('.ymc-clue').
                    removeClass('ymc-active');

                crossword.find('.ymc-square').removeClass('ymc-highlighted-line');
                crossword.find('.ymc-crossword .ymc-row .ymc-first-letter .ymc-num').each(function () {
                    if( num === parseInt($(this).text()) )
                    {
                        $(this).siblings('.ymc-char').focus();
                        let elem = $(this).closest('.ymc-first-letter');
                        let x = elem.data('x');
                        let y = elem.data('y');

                        wordsActive.forEach((el) => {
                            if( x === el.x && y === el.y && dir === el.dir ) {
                                if (el.dir === 0 ) {
                                    for (let z = el.x; z < el.char.length + el.x; z++) {
                                        crossword.find('.ymc-square[data-x="' + z + '"][data-y="' + y + '"]').addClass('ymc-highlighted-line');
                                    }
                                }
                                else {
                                    for (let z = el.y; z < el.char.length + el.y; z++) {
                                        crossword.find('.ymc-square[data-y="' + z + '"][data-x="' + x + '"]').addClass('ymc-highlighted-line');
                                    }
                                }
                            }
                        });
                    }
                });
            });

            crossword.find('.ymc-actions .ymc-button-pause').on("click", function (e) {
                Timer.pause( crossword );
                $(this).addClass('active').siblings('.ymc-button').removeClass('active');
                sessionStorage.removeItem('_ymc_timer');
            });

            crossword.find('.ymc-actions .ymc-button-start').on("click", function (e) {
                Timer.start( crossword );
                $(this).addClass('active').siblings('.ymc-button').removeClass('active');
            });

            crossword.find('.ymc-actions .ymc-button-reset').on("click", function (e) {
                Timer.reset( crossword );
                $(this).addClass('active').siblings('.ymc-button').removeClass('active');
                sessionStorage.removeItem('_ymc_timer');
            });

            crossword.find('.ymc-actions .ymc-button-reload').on("click", function (e) {
                $(this).addClass('active').siblings('.ymc-button').removeClass('active');
                document.location.reload();
            });

        }

    });

}( jQuery ));