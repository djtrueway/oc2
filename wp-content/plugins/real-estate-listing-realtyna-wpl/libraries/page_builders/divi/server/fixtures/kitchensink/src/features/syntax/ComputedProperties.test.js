/**
 * Copyright (c) 2018-present, Elegant Themes, Inc.
 * Copyright (c) 2015-2018, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

import React from 'react';
import ReactDOM from 'react-dom';
import ComputedProperties from './ComputedProperties';

describe('computed properties', () => {
  it('renders without crashing', () => {
    const div = document.createElement('div');
    return new Promise(resolve => {
      ReactDOM.render(<ComputedProperties onReady={resolve} />, div);
    });
  });
});
