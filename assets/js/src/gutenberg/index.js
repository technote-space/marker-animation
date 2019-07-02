import { Common, RichText } from '@technote-space/register-grouped-format-type';
import { registerSettingFormat, applyStyles } from './utils';
import { getDefaultFormatGroupProps, getSettingFormatGroupProps, getDefaultFormatButtonProps } from './utils';

const { registerFormatTypeGroup, registerGroupedFormatType } = RichText;
const { getToolbarButtonProps } = Common.Helpers;

applyStyles();

registerFormatTypeGroup( ...getDefaultFormatGroupProps() );

registerFormatTypeGroup( ...getSettingFormatGroupProps() );

registerGroupedFormatType( getToolbarButtonProps( ...getDefaultFormatButtonProps() ) );

Object.keys( markerAnimationParams.settings ).forEach( key => registerSettingFormat( markerAnimationParams.settings[ key ] ) );

/** @var {{settings: {}}} markerAnimationParams */
