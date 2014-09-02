use strict;
use Data::Dumper;

my @a = (1,2,3);
my $b = {};

@{$b}{@a} = () x @a;

warn Dumper $b;



