/* eslint-disable no-magic-numbers */
import React from 'react';
import { mount } from 'enzyme';
import toJson from 'enzyme-to-json';
import MyTextControl from '../../src/gutenberg/components/text-control';

describe('MyTextControl', () => {
	it('should render text control', () => {
		const onChange = jest.fn(value => {
			expect(value).toBe('xyz');
		});
		const args     = {
			value: {
				start: 0,
				end: 1,
				text: 'test',
			},
		};
		const wrapper  = mount(<MyTextControl
			id='test-id'
			key='test-key'
			label='test-label'
			args={args}
			value='abc'
			onChange={onChange}
		/>);

		expect(toJson(wrapper, { mode: 'deep' })).toMatchSnapshot('render1');
		expect(wrapper.find('#test-id').hostNodes()).toHaveLength(1);

		wrapper.find('#test-id').hostNodes().simulate('change', { target: { value: 'xyz' } });
		expect(onChange).not.toBeCalled();
		expect(toJson(wrapper, { mode: 'deep' })).toMatchSnapshot('changed1');

		wrapper.find('#test-id').hostNodes().simulate('blur');
		expect(onChange).toBeCalled();
		expect(toJson(wrapper, { mode: 'deep' })).toMatchSnapshot('blur1');
	});

	it('should render text control', () => {
		const onChange = jest.fn();
		const args     = {
			value: {
				start: 0,
				end: 1,
				text: 'test',
			},
		};
		const wrapper  = mount(<MyTextControl
			id='test-id'
			key='test-key'
			label='test-label'
			args={args}
			value='abc'
			onChange={onChange}
		/>);

		expect(toJson(wrapper, { mode: 'deep' })).toMatchSnapshot('render2');
		expect(wrapper.find('#test-id').hostNodes()).toHaveLength(1);

		wrapper.find('#test-id').hostNodes().simulate('blur');
		expect(onChange).not.toBeCalled();
		expect(toJson(wrapper, { mode: 'deep' })).toMatchSnapshot('blur2');
	});
});
