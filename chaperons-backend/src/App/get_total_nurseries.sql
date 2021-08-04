drop function if exists get_total_nurseries;

delimiter $$

create function get_total_nurseries (
    family_id int, radius int)
returns int
begin

    declare family_lng double;
    declare family_lat double;
    declare lng1 float;
    declare lng2 float;
    declare lat1 float;
    declare lat2 float;
    declare distance_unit float;
    declare map_id int;
    declare show_dsp int;
    declare show_mac int;
    declare show_micro int;
    declare show_partners int;
    declare max_results int;
    declare max_radius int;
    declare total int;
    declare total_distance float;

    -- get family coordinates
    select a.longitude, a.latitude, f.map_id into family_lng, family_lat, map_id from address a
    inner join family f on f.address_id=a.id
    where f.id=family_id;

    if (family_lat is null) then
        return null;
    else

        -- get map options
        select m.show_dsp, m.show_mac, m.show_micro, m.show_partners, m.nurseries_by_family, m.nurseries_max_distance
        into show_dsp, show_mac, show_micro, show_partners, max_results, max_radius
        from map m where m.id=map_id;

        set distance_unit = 111045.0;

        set lng1 = family_lng - (radius / (distance_unit * COS(RADIANS(family_lat))));
        set lng2 = family_lng + (radius / (distance_unit * COS(RADIANS(family_lat))));
        set lat1 = family_lat  - (radius / distance_unit);
        set lat2 = family_lat  + (radius / distance_unit);

        select count(n.id)
        into total
        from nursery n
        inner join address a on n.address_id=a.id
        where a.latitude between lat1 and lat2
          and a.longitude between lng1 and lng2
          and (distance_unit
            * DEGREES(ACOS(COS(RADIANS(family_lat))
            * COS(RADIANS(a.latitude))
            * COS(RADIANS(family_lng - a.longitude))
            + SIN(RADIANS(family_lat))
            * SIN(RADIANS(a.latitude))))) < radius
          and (
            (show_dsp = 1 or n.nature != 'DSP')
            and (show_dspc = 1 or n.nature != 'DSPC')
            and (show_partners = 1 or n.nature != 'PARTNER')
            and (show_mac = 1 or (n.nature != 'CEP' or n.type != 'MAC'))
            and (show_micro = 1 or (n.nature != 'CEP' or n.type != 'MICRO'))
            and (show_other = 1 or (n.nature NOT IN ('DSP', 'DSPC', 'PARTNER') or (n.nature = 'CEP' AND n.type NOT IN ('MAC', 'MICRO'))))
          );

        return total;
    end if;

end

$$

delimiter ;
