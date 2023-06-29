// ####################################################
// rebar
// ####################################################
// 削除ボタン処理
const deleteInput = (compId, display_order) => {
    console.log('delete')
    $(`#comp-len-${compId}-${display_order}`).val("");
    $(`#comp-num-${compId}-${display_order}`).val("");
}

// formのテーブル要素作成
const createFormTableEl = () => {
    return $('<table>', {
        'class': 'mt-2 input-table',
    })
}

// formのテーブルタイトル一覧取得
const getFormTableTitle = () => {
    return ['NO', '長さ', '本数', '削除'];
}

// formのテーブルヘッド要素作成
const createFormTableHeadEl = (wClass, title, hidden="") => {
    return $('<th>', {
        'class': `comp-div-th ${wClass} ${hidden}`
    }).append(title)
}

// formの大外枠の作成
const createComponentFormDiv = (idName) => {
    return $('<div>', {
        id: idName,
        'class': "comp-div bg-white comp-div-outline"
    }).appendTo('#CompForm')
}

// 部材の名前を設置
const setComponentName = (name, addDiv) => {
    $('<p class="comp-div-title">').text(name).appendTo(addDiv);
}

// 選択された(されている)部材IDをhiddenに追加
const createComponentIdHidden = (id, addDiv) => {
    return $('<input>', {
        type: 'hidden',
        name: `input[comp_${id}][id]`,
        value: id
    }).appendTo(addDiv)
}

// 選択された(されている)部材名をhiddenに追加
const createComponentNameHidden = (id, name, addDiv) => {
    return $('<input>', {
        type: 'hidden',
        name: `input[comp_${id}][name]`,
        value: name
    }).appendTo(addDiv)
}

// テーブル行のクラス取得
const getRowBackGroundColor = (displayOrder) => {
    return displayOrder % 2 === 0 ? "main-bg-color" : '' ;
}

// 単位作成
const createUnitSpanEl = (umitName, addTd) => {
    $('<span>', {
        'class': 'unit',
    }).text(umitName).appendTo(addTd)
}

// 0埋めの4桁数字を作成
const createZeroForth = (displayOrder) => {
    let showOrder = "000" + displayOrder;
    return showOrder.substr(showOrder.length - 4);
}

// テーブル行のNo列の作成
const createTdNo = (bgClass, order, tr) => {
    $('<td>', {
        'class': `${bgClass} p-1 border-r-1 border-DADADA w-10per text-center`,
    }).text(order).appendTo(tr)
}

// テーブル行のNo以外の作成
const createTd = (bgClass, tr, center="") => {
    return $('<td>', {
        'class': `${bgClass} px-3 border-r-1 border-DADADA relative ${center}`,
    }).appendTo(tr)
}

// 削除ボタンの作成
const createRemoveBtnIcon = (path, id, displayOrder, tdDelete) => {
    $('<img>', {
        src: path,
        height: '16px',
        width: '16px',
        on: {
            click: () => {deleteInput(id, displayOrder)}
        },
        'class': 'delete-icon'
    }).appendTo(tdDelete)
}

// 長さと本数のinput要素を作成
const createInputNumberEl = (id, displayOrder, inputValue, td, inputName) => {
    const category = inputName === 'length' ? 'len' : 'num'
    $('<input>', {
        type: 'number',
        name: `input[comp_${id}][data][${displayOrder}][${inputName}]`,
        value: inputValue,
        id: `comp-${category}-${id}-${displayOrder}`
    }).appendTo(td)
}
// 長さと本数のテキスト要素を作成
const createTextNumberEl = (text, td) => {
    $('<p class="tracking-wider font-semibold">', {
    }).text(text).appendTo(td)
}

// 部材毎の入力内容のhidden要素
const createComponentInputHiddenEl = (id, displayOrder, inputValue, inputName, tr) => {
    $('<input>', {
        type: 'hidden',
        name: `input[comp_${id}][data][${displayOrder}][${inputName}]`,
        value: inputValue
    }).appendTo(tr)
}

// formのデフォルトの値を取得
const getInitialFormData = (order) => {
    return {
        display_order: order, 
        length: null, 
        number: null
    }
}

// 作成する行数を取得
const getRowCount = (inputData) => {
    let count = inputData ? Object.keys(inputData).length : 10;

    if (count < 10) {
        count = 10;
    }

    return count
}

// テーブル行追加ロジック
const addForm = (compId, id, callback) => {
    const CompoTable = $(`#${compId} table`)
    const compoTableRowCount = CompoTable.children().length;
    const initialRow = getInitialFormData(compoTableRowCount)
    $(callback(id, initialRow)).appendTo(CompoTable)
}

// 追加ボタン作成
const createAddBtn = (compId, id, createComoTableRowEl, compDiv, op="") => {
    return $('<button>', {
        type: 'button',
        'class': 'w-26px h-26px p-4px border-2 flex items-center justify-center mt-4 ml-4' + op,
        on: {
            click: () => {addForm(compId, id, createComoTableRowEl)}
        }
    }).text('＋').appendTo(compDiv)
}