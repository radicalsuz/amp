update calendar a, states b set a.lstate = b.state where a.lstate = b.id;
update calendar set lstate = '' where lstate = 'Select State';
update calendar set state2 = '' where state2 = 'Select State';
