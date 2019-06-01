const { TextControl } = wp.components;
const { compose, withState } = wp.compose;

/**
 * @param {?string} initialValue initial value
 * @param {?string} stateValue state value
 * @param {function} setState set state
 * @param {function} onChange on change
 * @param {string} id id
 * @param {string} label label
 * @param {string} value value
 * @returns {Component} text control
 * @constructor
 */
const MyTextControl = ( { initialValue, stateValue, setState, onChange, id, label, value } ) => {
	if ( null === initialValue ) {
		setState( { initialValue: value } );
		setState( { stateValue: value } );
	}
	return <TextControl
		id={ id }
		label={ label }
		value={ stateValue }
		onChange={ ( value ) => {
			setState( { stateValue: value } );
		} }
		onBlur={ () => {
			if ( initialValue !== stateValue ) {
				onChange( stateValue );
			}
		} }
	/>;
};

export default compose(
	withState( {
		stateValue: null,
		initialValue: null,
	} ),
)( MyTextControl );
