import React from 'react';
import { TextControl } from '@wordpress/components';
import { useState, useMemo, useCallback } from '@wordpress/element';
import { isEqual } from 'lodash';

/**
 * @param {function} onChange on change
 * @param {object} args args
 * @param {string} id id
 * @param {string} label label
 * @param {string} value value
 * @returns {Component} text control
 * @constructor
 */
const MyTextControl = ({ onChange, args, id, label, value }) => {
  const [stateArgs, setStateArgs]       = useState(undefined);
  const [stateValue, setStateValue]     = useState('');
  const [initialValue, setInitialValue] = useState('');
  const onBlur                          = useCallback(() => {
    if (initialValue !== stateValue) {
      onChange(stateValue);
    }
  });


  if (undefined === stateArgs || !isEqual([args.value.start, args.value.end, args.value.text], [stateArgs.value.start, stateArgs.value.end, stateArgs.value.text])) {
    setStateArgs(args);
    setStateValue(value);
    setInitialValue(value);
  }

  return useMemo(() => <TextControl
    id={id}
    label={label}
    value={stateValue}
    onChange={setStateValue}
    onBlur={onBlur}
  />, [id, stateValue]);
};

export default MyTextControl;
