<?php
/**
 * @copyright Copyright (c) 2019 Robin Appelman <robin@icewind.nl>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Icewind\SMB\Test;

use Icewind\SMB\System;
use Icewind\SMB\TimeZoneProvider;

class TimeZoneProviderTest extends TestCase {
	/** @var System|\PHPUnit_Framework_MockObject_MockObject */
	private $system;
	/** @var TimeZoneProvider */
	private $provider;

	protected function setUp() {
		parent::setUp();

		$this->system = $this->getMock('Icewind\SMB\System');
	}

	private function getDummyCommand($output) {
		return "echo '$output' || false";
	}

	public function testFQDN() {
		$this->system->method('getNetPath')
			->willReturn($this->getDummyCommand("+800"));
		$this->system->method('getDatePath')
			->willReturn($this->getDummyCommand("+700"));
		$this->provider = new TimeZoneProvider('foo.bar.com', $this->system);

		$this->assertEquals('+800', $this->provider->get());
	}

	public function testNoNet() {
		$this->system->method('getNetPath')
			->willReturn(false);
		$this->system->method('getDatePath')
			->willReturn($this->getDummyCommand("+700"));
		$this->provider = new TimeZoneProvider('foo.bar.com', $this->system);

		$this->assertEquals('+700', $this->provider->get());
	}

	public function testNoNetNoDate() {
		$this->system->method('getNetPath')
			->willReturn(false);
		$this->system->method('getDatePath')
			->willReturn(false);
		$this->provider = new TimeZoneProvider('foo.bar.com', $this->system);

		$this->assertEquals(date_default_timezone_get(), $this->provider->get());
	}
}
