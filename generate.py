def generate_sql_queries(table_name, college, branch, year, present_year, exam_type, num_entries):
    queries = []
    for i in range(1, num_entries + 1):
        subject = f'Subject {i}'
        link = f'link{i}'
        
        query = f"""
        INSERT INTO {table_name} (college, branch, year, present_year, subject, type, link)
        VALUES ('{college}', '{branch}', {year}, {present_year}, '{subject}', '{exam_type}', '{link}');
        """
        queries.append(query.strip())
    
    return "\n".join(queries)

# Customize these parameters
table_name = "papers" # Replace with your actual table name
college = "Pandit Deendayal Energy University"
branch = "Mechanical Engineering"
year = 2023
present_year = 4
exam_type = "End Sem"
num_entries = 59  # Set the number of entries you want to generate

# Generate and print SQL queries
sql_queries = generate_sql_queries(table_name, college, branch, year, present_year, exam_type, num_entries)
print(sql_queries)
