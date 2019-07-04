const { TextControl } = wp.components;
const { compose, withState } = wp.compose;
const { isEqual } = window.lodash;

/**
 * @param {object?} stateArgs state args
 * @param {string?} initialValue initial value
 * @param {string?} stateValue state value
 * @param {function} setState set state
 * @param {function} onChange on change
 * @param {object} args args
 * @param {string} id id
 * @param {string} label label
 * @param {string} value value
 * @returns {Component} text control
 * @constructor
 */
const MyTextControl = ( { stateArgs, initialValue, stateValue, setState, onChange, args, id, label, value } ) => {
	if ( undefined === stateArgs || ! isEqual( [ args.value.start, args.value.end, args.value.text ], [ stateArgs.value.start, stateArgs.value.end, stateArgs.value.text ] ) ) {
		setState( { stateArgs: args } );
		setState( { initialValue: value } );
		setState( { stateValue: value } );
	}
	return <TextControl
		id={ id }
		label={ label }
		value={ stateValue }
		onChange={ value => setState( { stateValue: value } ) }
		onBlur={ () => {
			if ( initialValue !== stateValue ) {
				onChange( stateValue );
			}
		} }
	/>;
};

export default compose(
	withState( {
		stateArgs: undefined,
		stateValue: '',
		initialValue: '',
	} ),
)( MyTextControl );
