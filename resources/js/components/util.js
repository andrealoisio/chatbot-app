const defaulCurrencies = require('./defaultCurrencies').defaultCurrencies

const extractMoney = (text) => {
    let onlyNumbers = text.replace(/[^\.0-9]/g,"");
    if (isNaN(onlyNumbers)) {
        return undefined
    }
    return Number(onlyNumbers)
}

const extractCurrencyCode = (text, currencyList = defaulCurrencies) => {
    const currencycode = text.split(" ").filter(word => word.length === 3 && currencyList.indexOf(word.toUpperCase()) !== -1).map(code => code.toUpperCase())
    return currencycode[0];
}

exports.extractMoney = extractMoney
exports.extractCurrencyCode = extractCurrencyCode
