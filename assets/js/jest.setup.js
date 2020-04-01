import { setupGlobal } from '@technote-space/gutenberg-test-helper';

setupGlobal({
	globalParams: {
		markerAnimationParams: {
			translate: {},
			class: 'test-marker-animation-class',
			prefix: 'prefix_',
			details: {
				'test1-1': {
					form: 'input/checkbox',
					attributes: {
						'data-value': 'test1',
						'data-option_name': 'test1-option',
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
				color: {
					form: 'color',
					value: 'blue',
					attributes: {},
				},
				stripe: {
					form: 'input/checkbox',
					detail: {
						value: true,
					},
					attributes: {},
				},
			},
		},
	},
});

{
	const editorDiv = document.createElement('div');
	editorDiv.id    = 'editor';

	const addChild   = (tag, dataset) => {
		const child     = document.createElement(tag);
		child.className = markerAnimationParams.class;
		Object.keys(dataset).forEach(key => {
			child.dataset[ key ] = dataset[ key ];
		});
		editorDiv.append(child);
	};
	const getDataKey = key => `${global.markerAnimationParams.prefix}${key}`;

	addChild('div', { [ getDataKey('color') ]: 'red', test: '123' });
	addChild('div', { [ getDataKey('font_weight') ]: 'bold' });

	document.body.appendChild(editorDiv);
}
