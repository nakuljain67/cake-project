<?php

echo $this->Form->create();
// The following generates a Text input
echo $this->Form->control('username');
// The following generates a Password input
echo $this->Form->control('password');
// Assuming 'approved' is a datetime or timestamp field the following
//generates: Day, Month, Year, Hour, Minute
echo $this->Form->control('approved');
// The following generates a Textarea element
echo $this->Form->control('quote');

echo $this->Form->button('Add');
echo $this->Form->end();
?>