global.markerAnimationParams = {
	class: 'test-class1',
	prefix: 'prefix-',
	details: {
		'test1-1': {
			form: 'input/checkbox',
			attributes: {
				'data-value': 'test1',
			},
		},
		'test1-2': {
			form: 'input/checkbox',
			attributes: {
				'data-value': false,
			},
		},
		test2: {
			form: 'input/checkbox',
			attributes: {
				'data-value': 'test2',
				'data-option_value-1': 'value1',
				'data-option_value-0': 'value0',
			},
		},
		test3: {
			form: 'input/text',
			attributes: {
				'data-value': 'test3',
			},
		},
		test4: {
			form: 'select',
			attributes: {
				'data-value': 'test4',
			},
			options: { t1: 1, t2: 2 },
		},
		test5: {
			form: 'color',
			attributes: {
				'data-value': 'test5',
			},
		},
		test6: {
			form: 'color',
			ignore: true,
			attributes: {
				'data-value': 'test6',
			},
		},
	},
};
