/////////////////////////////////////////////////////////////////////////////////////
// DelayActivity
/////////////////////////////////////////////////////////////////////////////////////
DelayActivity = function()
{
	var ob = new BizProcActivity();
	ob.Type = 'DelayActivity';

	ob.CheckFields = function ()
	{
		if(!ob.Properties['TimeoutDuration'])
		{
			return false;
		}
		return true;
	}

	return ob;
}
