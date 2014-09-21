;(function(document){
    var oLabelElement
        , oInputElement
        , sOriginalText
        , oButtonElement
        , oContainerElement
        , sValue
    ;

    function getElement(p_sName) {
        return document.getElementsByClassName('JS-file-' + p_sName)[0];
    }

    function inputChangeHandler() {
        sValue = this.value.split(/[\\\\/]/).pop();

        if (sValue === ''){
            sValue = sOriginalText;
            oContainerElement.classList.add('active');
            oButtonElement.classList.remove('active');
        } else {
            oButtonElement.classList.add('active');
            oContainerElement.classList.remove('active');
        }
        oLabelElement.innerHTML = sValue;
    }

    function init(){
        oButtonElement = getElement('button');
        oContainerElement = getElement('container');
        oInputElement = getElement('input');
        oLabelElement = getElement('label');

        sOriginalText = oLabelElement.innerHTML;

    }

    init();

    oInputElement.onchange = inputChangeHandler;

}(document));