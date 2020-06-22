/* eslint-disable no-magic-numbers */
import {
  getPanelComponent,
  getCreateInspectorFunction,
  getDefaultFormatButtonProps,
  getDefaultFormatGroupProps,
  getSettingFormatGroupProps,
} from '../../src/gutenberg/utils/component';

describe('getPanelComponent', () => {
  it('should not get panel component', () => {
    expect(getPanelComponent({}, { isActive: false }, '', '', false)).toBeNull();
    expect(getPanelComponent({
      name: 'duration',
    }, { isActive: true }, '', '', true)).toBeNull();
    expect(getPanelComponent({
      name: 'delay',
    }, { isActive: true }, '', '', true)).toBeNull();
    expect(getPanelComponent({
      name: 'function',
    }, { isActive: true }, '', '', true)).toBeNull();
    expect(getPanelComponent({
      name: 'repeat',
    }, { isActive: true }, '', '', true)).toBeNull();
    expect(getPanelComponent({
      name: 'a',
      type: 'a',
      label: 'a',
    }, { isActive: true }, '', '', true)).toBeNull();
  });

  it('should get color pallet', () => {
    const component = getPanelComponent({
      name: 'colorpicker',
      type: 'colorpicker',
      label: 'colorpicker',
    }, {
      isActive: true,
    }, '', '', false);
    expect(typeof component).toBe('object');
    expect(component).toHaveProperty('key');
    expect(component).toHaveProperty('props');
    expect(component.key).toBe('marker-setting-colorpicker');
    expect(component.props).toHaveProperty('label');
    expect(component.props).toHaveProperty('colors');
    expect(component.props).toHaveProperty('onChange');
  });

  it('should get text control', () => {
    const em        = { type: 'em' };
    const strong    = { type: 'strong' };
    const color     = { type: 'color', attributes: { [ 'data-prefix_color' ]: 'red' } };
    const component = getPanelComponent({
      name: 'textbox',
      type: 'textbox',
      label: 'textbox',
    }, {
      isActive: true,
      value: {
        formats: [[color, em], [color, strong], []],
        text: 'one',
        start: 0,
        end: 2,
      },
      activeAttributes: {},
    }, 'textbox', 'a', false);
    expect(typeof component).toBe('object');
    expect(component).toHaveProperty('key');
    expect(component).toHaveProperty('props');
    expect(component.key).toBe('marker-setting-textbox');
    expect(component.props).toHaveProperty('label');
    expect(component.props).toHaveProperty('args');
    expect(component.props).toHaveProperty('onChange');
  });

  it('should get checkbox', () => {
    const component = getPanelComponent({
      name: 'checkbox',
      type: 'checkbox',
      label: 'checkbox',
    }, {
      isActive: true,
    }, '', '', false);
    expect(typeof component).toBe('object');
    expect(component).toHaveProperty('key');
    expect(component).toHaveProperty('props');
    expect(component.key).toBe('marker-setting-checkbox');
    expect(component.props).toHaveProperty('label');
    expect(component.props).toHaveProperty('checked');
    expect(component.props).toHaveProperty('onChange');
  });

  it('should get listbox', () => {
    const component = getPanelComponent({
      name: 'listbox',
      type: 'listbox',
      label: 'listbox',
    }, {
      isActive: true,
    }, '', '', false);
    expect(typeof component).toBe('object');
    expect(component).toHaveProperty('key');
    expect(component).toHaveProperty('props');
    expect(component.key).toBe('marker-setting-listbox');
    expect(component.props).toHaveProperty('label');
    expect(component.props).toHaveProperty('options');
    expect(component.props).toHaveProperty('onChange');
  });
});

describe('getDefaultFormatGroupProps', () => {
  it('should', () => {
    const props = getDefaultFormatGroupProps();
    expect(props).toHaveLength(2);
    expect(props[ 0 ]).toBe('marker-animation-default');
    expect(props[ 1 ]).toHaveProperty('toolbarGroup');
    expect(props[ 1 ]).toHaveProperty('label');
    expect(props[ 1 ]).toHaveProperty('inspectorSettings');
  });
});

describe('getSettingFormatGroupProps', () => {
  it('should', () => {
    const props = getSettingFormatGroupProps();
    expect(props).toHaveLength(2);
    expect(props[ 0 ]).toBe('marker-animation-setting');
    expect(props[ 1 ]).toHaveProperty('toolbarGroup');
    expect(props[ 1 ]).toHaveProperty('label');
    expect(props[ 1 ]).toHaveProperty('icon');
  });
});

describe('getDefaultFormatButtonProps', () => {
  it('should', () => {
    const props = getDefaultFormatButtonProps();
    expect(props).toHaveLength(4);
    expect(props[ 0 ]).toBe('marker-animation-default');
    expect(props[ 1 ]).toBe('marker-animation');
    expect(props[ 3 ]).toHaveProperty('title');
    expect(props[ 3 ]).toHaveProperty('className');
    expect(props[ 3 ]).toHaveProperty('inspectorGroup');
    expect(props[ 3 ]).toHaveProperty('useInspectorSetting');
    expect(props[ 3 ]).toHaveProperty('createInspector');
    expect(props[ 3 ]).toHaveProperty('attributes');
  });
});

describe('getCreateInspectorFunction', () => {
  it('should', () => {
    const em     = { type: 'em' };
    const strong = { type: 'strong' };
    const color  = { type: 'color', attributes: { [ 'data-prefix_color' ]: 'red' } };
    const record = {
      formats: [[color, em], [color, strong], []],
      text: 'one',
      start: 0,
      end: 2,
    };

    const func      = getCreateInspectorFunction();
    const inspector = func({
      args: {
        isActive: true,
        value: record,
        activeAttributes: {},
      },
      formatName: 'color',
    });
    expect(inspector).toHaveProperty('props');
    expect(inspector.props).toHaveProperty('children');
    expect(inspector.props.children).toHaveLength(2);
    expect(inspector.props.children[ 1 ]).toHaveProperty('props');
    expect(inspector.props.children[ 1 ].props).toHaveProperty('onClick');
    inspector.props.children[ 1 ].props.onClick();
  });
});
