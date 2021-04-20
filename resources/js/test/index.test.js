const assert = require('assert');
const translateToAction = require('../components/translateToAction').translateToAction
const util = require('../components/util')

describe("actions translation test", function () {
  it("should return login", function () {
    let action = translateToAction("log-in", true)
    assert.strictEqual(action.length, 1);
  });
  it("should return deposit", function () {
    let action = translateToAction("i want to deposit", true)
    assert.strictEqual(action.length, 1);
  });
  it("should return withdraw", function () {
    let action = translateToAction("i want to withdraw", true)
    assert.strictEqual(action.length, 1);
  });
  it("should return two actions", function () {
    let action = translateToAction("i want to withdraw deposit", true)
    assert.strictEqual(action.length, 2);
  });
  it("should extract number", function () {
    let number = util.extractMoney("I want to deposit 100.00 usd")
    assert.strictEqual(number, 100);
  });
  it("should not extract number", function () {
    let number = util.extractMoney("I want to deposit usd")
    assert.strictEqual(number, 0);
  });
  it("should extract currency code USD", function () {
    let currencyCode = util.extractCurrencyCode("I want to deposit 100 usd", ["USD","BRL"])
    assert.strictEqual(currencyCode, "USD");
  });
  it("should extract currency code BRL", function () {
    let currencyCode = util.extractCurrencyCode("I want to deposit 100 bRl", ["USD","BRL"])
    assert.strictEqual(currencyCode, "BRL");
  });
  it("should not extract currency code", function () {
    let currencyCode = util.extractCurrencyCode("I want to deposit 100", ["USD","BRL"])
    assert.strictEqual(currencyCode, undefined);
  });
});