/* eslint-disable no-magic-numbers */
import { addStyleHelper, applyStyles, addStyle } from '../../src/gutenberg/utils/style';
import { Helpers } from '../../src/gutenberg/wrapper';

jest.mock('../../src/gutenberg/wrapper');

beforeEach(() => {
  markerAnimationParams.addedStyle = {};
});

describe('addStyleHelper', () => {
  it('should set styles', () => {
    [
      'thickness',
      'padding_bottom',
      'display',
      'background-position',
      'background-repeat',
    ].forEach(key => {
      let called = 0;
      const func = (expectedValue, expectedClassName) => (className, style, _key) => {
        expect(className).toBe(expectedClassName);
        expect(style).toEndWith(expectedValue);
        expect(_key).toBeUndefined();
        called++;
      };
      addStyleHelper(func('test1', 'test-marker-animation-class'), key, 'test1');
      expect(called).toBe(1);
      addStyleHelper(func('test1', 'test-marker-animation-class'), key, 'test1');
      expect(called).toBe(1);
      addStyleHelper(func('test2', 'test-marker-animation-class'), key, 'test2');
      expect(called).toBe(2);
      addStyleHelper(func('test2', 'test-class2'), key, 'test2', 'test-class2');
      expect(called).toBe(3);
    });
  });
  it('should set font_weight', () => {
    let called = 0;
    const func = (expectedValue, expectedClassName) => (className, style, _key) => {
      expect(className).toBe(expectedClassName);
      expect(style).toEndWith(expectedValue);
      expect(_key).toBeUndefined();
      called++;
    };
    addStyleHelper(func('normal', 'test-marker-animation-class'), 'font_weight', 0);
    expect(called).toBe(1);
    addStyleHelper(func('normal', 'test-marker-animation-class'), 'font_weight', 0);
    expect(called).toBe(1);
    addStyleHelper(func('normal', 'test-marker-animation-class'), 'font_weight', 'null');
    expect(called).toBe(2);
    addStyleHelper(func('bold', 'test-marker-animation-class'), 'font_weight', 'bold');
    expect(called).toBe(3);
    addStyleHelper(func('bold', 'test-class2'), 'font_weight', 'bold', 'test-class2');
    expect(called).toBe(4);
  });
  it('should not set color', () => {
    let called = 0;
    const func = () => {
      called++;
    };
    addStyleHelper(func, 'color', 'red');
    expect(called).toBe(0);
  });
  it('should set color', () => {
    let called = 0;
    const func = (expectedValue, expectedClassName) => (className, style, _key) => {
      expect(className).toBe(expectedClassName);
      expect(style).toStartWith(expectedValue);
      expect(_key).toBeUndefined();
      called++;
    };
    addStyleHelper(func('background-image:linear-gradient', 'test-marker-animation-class'), 'color', 'red', undefined, false, false);
    expect(called).toBe(1);
    addStyleHelper(func('background-image:repeating-linear-gradient', 'test-class2'), 'color', 'red', 'test-class2', false, true);
    expect(called).toBe(2);
  });
  it('should set stripe color', () => {
    let called = 0;
    const func = () => {
      called++;
    };
    addStyleHelper(func, 'color', 'red', 'test-class3', true, true);
    expect(called).toBe(2);
    addStyleHelper(func, 'color', 'red', 'test-class4', true, false);
    expect(called).toBe(4);
  });
});

describe('applyStyles', () => {
  it('should setup styles', () => {
    jest.spyOn(Helpers, 'editorReady').mockImplementation((callback) => callback());
    applyStyles();
    applyStyles();
    window.document.dispatchEvent(new Event('DOMContentLoaded', {
      bubbles: true,
      cancelable: true,
    }));
    expect(Array.from(document.head.children).filter(el => {
      if (!el.sheet || !el.sheet.cssRules) {
        return false;
      }
      return el.sheet.cssRules.filter(rule => rule.selectorText && rule.selectorText.startsWith('body #editor .test-marker-animation-class')).length;
    })).toHaveLength(5);
  });
});

describe('addStyle', () => {
  it('should add styles', () => {
    addStyle('color', 'blue', 'test-class5', false, true, true, false);
    addStyle('thickness', '1em', 'test-class6', false);
    expect(Array.from(document.head.children).filter(el => {
      if (!el.sheet || !el.sheet.cssRules) {
        return false;
      }
      return el.sheet.cssRules.filter(rule => rule.selectorText && rule.selectorText.startsWith('body #editor .test-class5')).length;
    })).toHaveLength(2);
    expect(Array.from(document.head.children).filter(el => {
      if (!el.sheet || !el.sheet.cssRules) {
        return false;
      }
      return el.sheet.cssRules.filter(rule => rule.selectorText && rule.selectorText.startsWith('body #editor .test-class6')).length;
    })).toHaveLength(1);
  });
});
